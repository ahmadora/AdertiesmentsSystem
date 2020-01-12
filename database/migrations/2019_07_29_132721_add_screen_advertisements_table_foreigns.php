<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScreenAdvertisementsTableForeigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('screen_advertisements', function (Blueprint $table) {
            $table->foreign('screen_id')->references('id')
                ->on('screens')->onDelete('cascade');
            $table->foreign('advertisement_id')->references('id')
                ->on('advertisements')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('screen_advertisements', function (Blueprint $table) {
            $table->dropForeign('screen_id');
            $table->dropForeign('advertisement_id');
        });
    }
}
