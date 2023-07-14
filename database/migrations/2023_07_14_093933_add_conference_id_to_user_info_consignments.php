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
        Schema::table('user_info_consignments', function (Blueprint $table) {
            $table->foreignIdFor(Conference::class)->nullable()->after('user_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_info_consignments', function (Blueprint $table) {
            $table->dropForeignIdFor(Conference::class);
            $table->dropColumn('conference_id');
        });
    }
};
