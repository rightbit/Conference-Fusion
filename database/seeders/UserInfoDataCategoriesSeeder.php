<?php

namespace Database\Seeders;

use App\Models\UserInfoDataCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserInfoDataCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserInfoDataCategory::create([
            'type' => 'social',
            'name' => 'facebook',
        ]);

        UserInfoDataCategory::create([
            'type' => 'social',
            'name' => 'twitter',
        ]);

        UserInfoDataCategory::create([
            'type' => 'social',
            'name' => 'instagram',
        ]);

        UserInfoDataCategory::create([
            'type' => 'personal',
            'name' => 'gender',
            'options' => 'female,male,non-binary,prefer not to say',
        ]);

        UserInfoDataCategory::create([
            'type' => 'partcipant',
            'name' => 'book-signing',
        ]);

    }
}
