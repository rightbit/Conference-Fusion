<?php

use App\Models\Conference;
use App\Models\SessionStatus;
use App\Models\Track;
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
        Schema::create('conference_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('participant_notes')->nullable();
            $table->text('staff_notes')->nullable();
            $table->text('seed_questions')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedInteger('duration_minutes')->default('45');
            $table->foreignIdFor(SessionStatus::class)->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('registration_required')->default(0);
            $table->unsignedInteger('max_registration')->default(0);
            $table->unsignedInteger('attendance')->nullable();
            $table->foreignIdFor(Conference::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Track::class)->nullable()->constrained()->nullOnDelete();
            $table->dateTime('proposed_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conference_sessions');
    }
};
