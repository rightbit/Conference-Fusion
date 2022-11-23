<?php

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
        Schema::create('user_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('badge_name');
            $table->string('contact_email')->nullable();
            $table->text('biography')->nullable();
            $table->text('notes')->nullable();
            $table->string('website')->nullable();
            $table->json('social_data')->nullable();
            $table->json('personal_data')->nullable();
            $table->json('participant_data')->nullable();
            $table->string('profile_image')->nullable();
            $table->boolean('recording_permission')->default('1');
            $table->boolean('share_email_permission')->default('0');
            $table->boolean('agree_to_terms')->default('0');
            $table->timestamps();
            //Indexes
            $table->index('badge_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_infos');
    }
};
