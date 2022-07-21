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
        SessionStatus::create(['status' => 'Ready for Call','save_history' => 1]);
        SessionStatus::create(['status' => 'User Suggested', 'save_history' => 1]);
        SessionStatus::create(['status' => 'Participants Assigned', 'save_history' => 1]);
        SessionStatus::create(['status' => 'Scheduled', 'save_history' => 1]);
        SessionStatus::create(['status' => 'Canceled']);
        SessionStatus::create(['status' => 'Deleted']);
    }
}
