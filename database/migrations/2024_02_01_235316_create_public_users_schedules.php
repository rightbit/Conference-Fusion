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
        Schema::create('public_users_schedules', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignIdFor(ConferenceSchedule::class)->nullable()->constrained()->nullOnDelete();
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_users_schedules');
    }
};
