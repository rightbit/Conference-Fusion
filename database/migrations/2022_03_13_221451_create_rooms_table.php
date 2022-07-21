<?php

use App\Models\Conference;
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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(Conference::class)->constrained()->cascadeOnDelete();
            $table->integer('capacity');
            $table->boolean('has_av');
            $table->text('notes')->nullable();
            $table->unsignedInteger('display_order');
            $table->timestamps();
            // Indexes
            $table->unique(['name', 'conference_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms');
    }
};
