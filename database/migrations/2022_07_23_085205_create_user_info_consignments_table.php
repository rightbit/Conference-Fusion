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
        Schema::create('user_info_consignments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('book1_title')->nullable();
            $table->string('book1_author')->nullable();
            $table->string('book1_isbn')->nullable();
            $table->string('book2_title')->nullable();
            $table->string('book2_author')->nullable();
            $table->string('book2_isbn')->nullable();
            $table->string('book3_title')->nullable();
            $table->string('book3_author')->nullable();
            $table->string('book3_isbn')->nullable();
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
        Schema::dropIfExists('user_info_consignments');
    }
};
