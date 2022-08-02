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
            'label' => 'Facebook',
        ]);

        UserInfoDataCategory::create([
            'type' => 'social',
            'name' => 'twitter',
            'label' => 'Twitter',
        ]);

        UserInfoDataCategory::create([
            'type' => 'social',
            'name' => 'instagram',
            'label' => 'Instagram',
        ]);

        UserInfoDataCategory::create([
            'type' => 'personal',
            'name' => 'gender',
            'label' => 'Gender',
            'options' => 'female,male,non-binary,prefer not to say',
        ]);

        UserInfoDataCategory::create([
            'type' => 'participant',
            'name' => 'book-signing',
            'label' => 'Participate in a book signing',
        ]);

    }
}
