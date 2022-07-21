<?php

namespace Database\Seeders;

use App\Models\ConferenceSession;
use Illuminate\Database\Seeder;

class ConferenceSessionFactorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ConferenceSession::factory()->count(350)->create();
    }
}
