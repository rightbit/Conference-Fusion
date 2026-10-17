<?php

namespace Tests\Feature;

use App\Models\Conference;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SchedulePublicationTest extends TestCase
{
    private Conference $conference;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Never migrate, truncate, or seed the developer's populated database.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        $this->artisan('migrate', ['--database' => 'sqlite'])->assertExitCode(0);

        $this->admin = User::factory()->create();
        DB::table('user_permissions')->insert(['user_id' => $this->admin->id, 'permission' => 'admin']);
        $this->conference = Conference::create(['name' => 'Test conference', 'short_name' => 'TestCon']);
        DB::table('site_configs')->insert([
            'key' => 'default_conference_id', 'value' => $this->conference->id,
            'description' => 'Default conference', 'last_updated_user_id' => $this->admin->id,
        ]);
    }

    public function test_new_and_existing_conferences_default_to_unpublished(): void
    {
        $this->assertFalse($this->conference->fresh()->schedule_published);

        $migration = require database_path('migrations/2026_10_17_120000_add_schedule_published_to_conferences_table.php');
        $migration->down();
        $id = DB::table('conferences')->insertGetId(['name' => 'Existing', 'short_name' => 'Existing']);
        $migration->up();

        $this->assertFalse(Conference::findOrFail($id)->schedule_published);
    }

    public function test_only_admins_can_publish_and_invalid_values_are_rejected(): void
    {
        $url = $this->publicationUrl();
        $this->patchJson($url, ['schedule_published' => true])->assertUnauthorized();

        $staff = User::factory()->create();
        DB::table('user_permissions')->insert(['user_id' => $staff->id, 'permission' => 'view_admin']);
        $this->actingAs($staff)->patchJson($url, ['schedule_published' => true])->assertForbidden();

        $this->actingAs($this->admin)->patchJson($url, [])->assertUnprocessable();
        $this->patchJson($url, ['schedule_published' => 'publish'])->assertUnprocessable();
        $this->assertFalse($this->conference->fresh()->schedule_published);
    }

    public function test_publish_and_unpublish_are_idempotent_and_leave_session_statuses_unchanged(): void
    {
        $this->addScheduleFixtures();
        $before = DB::table('conference_sessions')->orderBy('id')->get()->toJson();
        $other = Conference::create(['name' => 'Other', 'short_name' => 'Other']);

        foreach ([true, true, false, false] as $published) {
            $this->actingAs($this->admin)->patchJson($this->publicationUrl(), ['schedule_published' => $published])
                ->assertOk()->assertJsonPath('data.schedule_published', $published);
            $this->assertSame($published, $this->conference->fresh()->schedule_published);
            $this->assertSame($before, DB::table('conference_sessions')->orderBy('id')->get()->toJson());
            $this->assertFalse($other->fresh()->schedule_published);
        }

        $this->getJson('/api/admin/tools/sync-scheduled-status/'.$this->conference->id)->assertNotFound();
    }

    public function test_unpublished_public_endpoint_hides_events_and_presenters_but_keeps_metadata(): void
    {
        $this->addScheduleFixtures();
        $this->getJson('/api/public-schedule')->assertOk()
            ->assertJsonPath('schedulePublished', false)
            ->assertJsonPath('conferenceName', 'TestCon')
            ->assertJsonPath('events', [])->assertJsonPath('presenters', [])
            ->assertJsonPath('eventPresenters', [])
            ->assertJsonCount(1, 'sponsors')->assertJsonCount(1, 'announcements');
    }

    public function test_published_public_endpoint_includes_only_visible_events_and_their_presenters(): void
    {
        $fixtures = $this->addScheduleFixtures();
        $this->actingAs($this->admin)->patchJson($this->publicationUrl(), ['schedule_published' => true])->assertOk();

        $this->getJson('/api/public-schedule')->assertOk()
            ->assertJsonPath('schedulePublished', true)
            ->assertJsonCount(1, 'events')->assertJsonCount(1, 'presenters')->assertJsonCount(1, 'eventPresenters')
            ->assertJsonPath('events.0.id', $fixtures['event'])
            ->assertJsonPath('presenters.0.id', $fixtures['presenter'])
            ->assertJsonPath('eventPresenters.0.eventId', $fixtures['event'])
            ->assertJsonPath('eventPresenters.0.presenterId', $fixtures['presenter']);

        $this->patchJson($this->publicationUrl(), ['schedule_published' => false])->assertOk();
        $this->getJson('/api/public-schedule')->assertJsonPath('events', [])->assertJsonPath('presenters', [])
            ->assertJsonPath('eventPresenters', []);
    }

    public function test_draft_endpoint_returns_all_public_track_entries_regardless_of_publication(): void
    {
        $this->addScheduleFixtures();
        foreach ([false, true, false] as $published) {
            $this->conference->schedule_published = $published;
            $this->conference->save();
            $this->getJson('/api/public-schedule-test')->assertOk()
                ->assertJsonPath('schedulePublished', $published)
                ->assertJsonCount(2, 'events')->assertJsonCount(2, 'presenters')
                ->assertJsonCount(2, 'eventPresenters');
        }
    }

    public function test_conference_creation_and_ordinary_edits_cannot_change_publication(): void
    {
        $fields = [
            'name' => 'Updated', 'short_name' => 'Updated', 'description' => 'Description',
            'time_zone' => '-7', 'start_date' => '2027-02-10', 'end_date' => '2027-02-13',
            'session_start_time' => '09:00', 'session_end_time' => '21:00',
            'schedule_published' => true,
        ];
        $this->actingAs($this->admin)->postJson('/api/admin/conference', $fields)
            ->assertCreated()->assertJsonPath('data.schedule_published', false);
        $this->putJson('/api/admin/conference/'.$this->conference->id, $fields + ['id' => $this->conference->id])
            ->assertOk()->assertJsonPath('data.schedule_published', false);
        $this->patchJson($this->publicationUrl(), ['schedule_published' => true])->assertOk();
        $fields['schedule_published'] = false;
        $this->putJson('/api/admin/conference/'.$this->conference->id, $fields + ['id' => $this->conference->id])
            ->assertOk()->assertJsonPath('data.schedule_published', true);
    }

    private function publicationUrl(): string
    {
        return '/api/admin/conference/'.$this->conference->id.'/schedule-publication';
    }

    private function addScheduleFixtures(): array
    {
        $scheduled = DB::table('session_statuses')->insertGetId(['status' => 'Scheduled']);
        $planning = DB::table('session_statuses')->insertGetId(['status' => 'Planning']);
        $track = DB::table('tracks')->insertGetId(['name' => 'Public', 'administrative' => false]);
        $internal = DB::table('tracks')->insertGetId(['name' => 'Internal', 'administrative' => true]);
        $room = DB::table('rooms')->insertGetId([
            'name' => 'Room', 'conference_id' => $this->conference->id, 'capacity' => 100, 'has_av' => true,
        ]);
        DB::table('conference_sponsors')->insert(['name' => 'Sponsor', 'conference_id' => $this->conference->id]);
        DB::table('conference_announcements')->insert(['title' => 'Announcement', 'conference_id' => $this->conference->id]);
        $visible = [];
        foreach ([[$scheduled, $track, true], [$planning, $track, true], [$scheduled, $internal, true], [$scheduled, $track, false]] as $i => [$status, $trackId, $hasSchedule]) {
            $session = DB::table('conference_sessions')->insertGetId([
                'name' => 'Session '.$i, 'conference_id' => $this->conference->id,
                'session_status_id' => $status, 'track_id' => $trackId,
            ]);
            $presenter = User::factory()->create();
            DB::table('session_interests')->insert([
                'conference_session_id' => $session, 'user_id' => $presenter->id, 'is_participant' => true,
            ]);
            if ($hasSchedule) {
                $event = DB::table('conference_schedules')->insertGetId([
                    'conference_id' => $this->conference->id, 'conference_session_id' => $session,
                    'track_id' => $trackId, 'room_id' => $room, 'date' => '2027-02-11', 'time' => '09:00',
                ]);
                if ($i === 0) {
                    $visible = ['event' => $event, 'presenter' => $presenter->id];
                }
            }
        }
        return $visible;
    }
}
