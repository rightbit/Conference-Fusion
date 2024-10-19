<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('These are setup scripts. They should only be run after the first migration. Running this after using the app will create duplicate entries, and in some cases crash.');
        if ($this->command->confirm('Do you *really* wish to continue?')) {

            //Add default user
            $this->call(UserSeeder::class);
            $this->command->info('Default user seeded...');

            //Make default user an Admin
            $this->call(AdminPermissionSeeder::class);
            $this->command->info('Admin permission seeded...');

            //Create site configs
            $this->call(SiteConfigSeeder::class);
            $this->command->info('Site configs seeded...');

            //Create schedule warnings
            $this->call(ScheduleWarningsSeeder::class);
            $this->command->info('Schedule warnings seeded...');

            //Create conference
            $this->call(ConferenceSeeder::class);
            $this->command->info('Default conference seeded...');

            //Create standard user info data categories
            $this->call(UserInfoDataCategoriesSeeder::class);
            $this->command->info('Default user info categories seeded...');

            //Create standard session statuses
            $this->call(SessionStatusSeeder::class);
            $this->command->info('Session statuses seeded...');

            //Create standard session types
            $this->call(SessionTypeSeeder::class);
            $this->command->info('Default session types seeded...');
            //Create standard session types

            $this->call(SessionSpecialEquipmentSeeder::class);
            $this->command->info('Default session equipment seeded...');

        } else {
            $this->command->info('Exiting without seeding');
        }

    }
}
