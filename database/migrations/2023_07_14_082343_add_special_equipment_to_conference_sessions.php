<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conference_sessions', function (Blueprint $table) {
            $table->string('special_equipment')->nullable()->after('proposed_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conference_sessions', function (Blueprint $table) {
            $table->dropColumn('special_equipment');
        });
    }
};
