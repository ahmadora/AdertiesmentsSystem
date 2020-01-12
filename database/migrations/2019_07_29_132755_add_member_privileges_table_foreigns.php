<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMemberPrivilegesTableForeigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_privileges', function (Blueprint $table) {
            $table->foreign('member_id')->references('id')
                ->on('workspace_members')->onDelete('cascade');
            $table->foreign('privilege_id')->references('id')
                ->on('privileges')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_privileges', function (Blueprint $table) {
            $table->dropForeign('member_id');
            $table->dropForeign('privilege_id');
        });
    }
}
