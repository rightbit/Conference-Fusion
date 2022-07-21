<?php

use App\Models\ConferenceSession;
use App\Models\User;
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
        Schema::create('session_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ConferenceSession::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->tinyInteger('interest_level')->default('1');
            $table->tinyInteger('experience_level')->default('1');
            $table->tinyInteger('panel_role')->default('1');
            $table->text('notes')->nullable();
            $table->boolean('will_moderate')->default('0');
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
        Schema::dropIfExists('session_interests');
    }
};
