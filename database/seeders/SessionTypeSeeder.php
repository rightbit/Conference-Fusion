<?php

namespace Database\Seeders;

use App\Models\SessionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SessionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SessionType::create(['id' => 1, 'name' => 'Panel']);
        SessionType::create(['id' => 2, 'name' => 'Presentation']);
        SessionType::create(['id' => 3, 'name' => 'Keynote']);
        SessionType::create(['id' => 4, 'name' => 'Workshop']);
        SessionType::create(['id' => 5, 'name' => 'Masterclass']);
        SessionType::create(['id' => 6, 'name' => 'Chat']);
        SessionType::create(['id' => 7, 'name' => 'Pitch']);
    }
}
