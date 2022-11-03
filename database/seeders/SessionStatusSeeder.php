<?php

namespace Database\Seeders;

use App\Models\SessionStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SessionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SessionStatus::create(['status' => 'Planning']);
        SessionStatus::create(['status' => 'Ready for Call']);
        SessionStatus::create(['status' => 'User Suggested']);
        SessionStatus::create(['status' => 'Participants Assigned']);
        SessionStatus::create(['status' => 'Scheduled']);
        SessionStatus::create(['status' => 'Not Used']);
        SessionStatus::create(['status' => 'Canceled']);
    }
}
