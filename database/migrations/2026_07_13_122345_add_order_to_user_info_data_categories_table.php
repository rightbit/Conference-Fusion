<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_info_data_categories', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(0)->after('required');
        });

        DB::table('user_info_data_categories')
            ->orderBy('id')
            ->get(['id'])
            ->each(function ($category, $index) {
                DB::table('user_info_data_categories')
                    ->where('id', $category->id)
                    ->update(['order' => $index + 1]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_info_data_categories', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
