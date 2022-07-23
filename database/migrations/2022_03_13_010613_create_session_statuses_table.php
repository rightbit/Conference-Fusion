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
        Schema::create('session_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->boolean('save_history')->default('0');
            $table->timestamps();
            //Indexes
            $table->unique('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('session_statuses');
    }
};
