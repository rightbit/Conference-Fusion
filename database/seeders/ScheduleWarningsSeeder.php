<?php

namespace Database\Seeders;

use App\Models\ScheduleWarnings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleWarningsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ScheduleWarnings::create([
            'key' => 'min_sessions',
            'value' => '3',
            'description' => 'The minimum number of sessions for a panelist',
            'last_updated_user_id' => 1,
        ]);

        ScheduleWarnings::create([
            'key' => 'max_sessions',
            'value' => '9',
            'description' => 'The maximum number of sessions for a panelist',
            'last_updated_user_id' => 1,
        ]);

        ScheduleWarnings::create([
            'key' => 'max_consecutive_sessions',
            'value' => '1',
            'description' => 'The number of sessions in a row for a panelist',
            'last_updated_user_id' => 1,
        ]);

        ScheduleWarnings::create([
            'key' => 'same_time_sessions',
            'value' => '1',
            'description' => 'Restrict a panelist to be in one session at the same time',
            'last_updated_user_id' => 1,
        ]);

        ScheduleWarnings::create([
            'key' => 'min_panelists',
            'value' => '3',
            'description' => 'The minimum number of panelists on a session',
            'last_updated_user_id' => 1,
        ]);

        ScheduleWarnings::create([
            'key' => 'max_panelists',
            'value' => '5',
            'description' => 'The maximum number of panelists on a session',
            'last_updated_user_id' => 1,
        ]);

        ScheduleWarnings::create([
            'key' => 'require_moderator',
            'value' => '1',
            'description' => 'Panels must have a moderator',
            'last_updated_user_id' => 1,
        ]);

        ScheduleWarnings::create([
            'key' => 'zero_participants',
            'value' => '1',
            'description' => 'Panels must have a panelists',
            'last_updated_user_id' => 1,
        ]);

        ScheduleWarnings::create([
            'key' => 'check_status',
            'value' => '1',
            'description' => 'Panels must have a status of scheduled or ready to schedule',
            'last_updated_user_id' => 1,
        ]);
    }
}
