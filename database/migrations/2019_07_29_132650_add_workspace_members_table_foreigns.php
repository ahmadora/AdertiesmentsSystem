<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWorkspaceMembersTableForeigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workspace_members', function (Blueprint $table) {
            $table->foreign('workspace_id')->references('id')
                ->on('workspaces')->onDelete('cascade');
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workspace_members', function (Blueprint $table) {
            $table->dropForeign('workspace_id');
            $table->dropForeign('user_id');
        });
    }
}
