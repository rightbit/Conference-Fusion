<?php

namespace Database\Seeders;

use App\Models\SessionSpecialEquipment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SessionSpecialEquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SessionSpecialEquipment::create([
            'equipment' => 'Projector'
        ]);

        SessionSpecialEquipment::create([
            'equipment' => 'Audio'
        ]);

        SessionSpecialEquipment::create([
            'equipment' => 'Whiteboard'
        ]);

    }
}
