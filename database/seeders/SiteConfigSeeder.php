<?php

namespace Database\Seeders;

use App\Models\SiteConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SiteConfig::create([
            'key' => 'site_name',
            'value' => 'Scheduler',
            'description' => 'The name of the website',
            'last_updated_user_id' => 1,
        ]);

        SiteConfig::create([
            'key' => 'contact_email',
            'value' => 'scheduling@example.com',
            'description' => 'The contact email for this site',
            'last_updated_user_id' => 1,
        ]);

        SiteConfig::create([
            'key' => 'default_conference_id',
            'value' => '1',
            'description' => 'The active conference selected for users/panelists',
            'last_updated_user_id' => 1,
        ]);

        SiteConfig::create([
            'key' => 'conference_roles',
            'value' => 'committee chair,committee member,track head,panelist,presenter,special guest,keynote speaker',
            'description' => 'List of roles for participants at the conference (Comma seperated)',
            'last_updated_user_id' => 1,
        ]);

        SiteConfig::create([
            'key' => 'default_session_duration',
            'value' => '50',
            'description' => 'Default length for sessions on the schedule',
            'last_updated_user_id' => 1,
        ]);

        SiteConfig::create([
            'key' => 'home_page_message',
            'value' => 'Please make sure the personal info in your profile is up to date.',
            'description' => 'Add a message displayed on the home page (Leave 0 for no message)',
            'last_updated_user_id' => 1,
        ]);

        SiteConfig::create([
            'key' => 'user_session_view_message',
            'value' => '0',
            'description' => 'Add a message displayed on the session page (Leave 0 for no message)',
            'last_updated_user_id' => 1,
        ]);
    }
}
