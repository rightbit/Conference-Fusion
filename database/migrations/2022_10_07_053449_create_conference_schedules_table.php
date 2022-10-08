<?php

use App\Models\Conference;
use App\Models\ConferenceSession;
use App\Models\Room;
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
        Schema::create('conference_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Conference::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ConferenceSession::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Track::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Room::class)->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('time');
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
        Schema::dropIfExists('conference_schedules');
    }
};
