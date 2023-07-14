<?php

namespace Database\Seeders;

use App\Models\SiteConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSigningConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SiteConfig::create([
            'key' => 'book_signing_module',
            'value' => '1',
            'description' => 'Include book signing event in the call',
            'last_updated_user_id' => 1,
        ]);
    }
}
