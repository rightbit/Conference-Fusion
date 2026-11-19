<?php

namespace App\Services;

use App\Models\Conference;
use App\Models\ConferenceSchedule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PublicScheduleSnapshot
{
    public function exists(Conference $conference): bool
    {
        return File::isFile($this->path($conference));
    }

    public function sync(Conference $conference): array
    {
        // Resolve resources now so public reads cannot trigger lazy database queries.
        $json = json_encode(ConferenceSchedule::snapshotSchedule($conference), JSON_THROW_ON_ERROR);
        $path = $this->path($conference);
        File::ensureDirectoryExists(dirname($path));
        File::replace($path, $json);

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    public function read(Conference $conference): array
    {
        if (!$this->exists($conference)) {
            abort_if($conference->schedule_published, 503, 'Schedule snapshot is unavailable. Please sync the session schedule.');
            $schedule = [
                'conferenceName' => $conference->short_name,
                'tracks' => [], 'locations' => [], 'events' => [],
                'presenters' => [], 'eventPresenters' => [], 'sponsors' => [], 'announcements' => [],
            ];
        } else {
            $schedule = json_decode(File::get($this->path($conference)), true, 512, JSON_THROW_ON_ERROR);
        }

        // Publication takes effect immediately without rebuilding the snapshot.
        $schedule['schedulePublished'] = $conference->schedule_published;
        if (!$conference->schedule_published) {
            $schedule['events'] = [];
            $schedule['presenters'] = [];
            $schedule['eventPresenters'] = [];
        }

        return $schedule;
    }

    private function path(Conference $conference): string
    {
        return Storage::disk('local')->path('schedule-snapshots/conference-'.$conference->id.'.json');
    }
}
