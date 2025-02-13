<?php

use App\Models\ConferenceSchedule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conference_schedule_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ConferenceSchedule::class)->nullable()->constrained()->nullOnDelete();
            $table->uuid()->nullable();
            $table->tinyInteger('overall_rating');
            $table->text('participant_ratings')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_schedule_ratings');
    }
};
