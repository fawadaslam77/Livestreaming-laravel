<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BreakUrlColumnInStreamColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_streams', function(Blueprint $table)
        {
            $table->dropColumn('url');
            $table->string('stream_app')->after('name');
            $table->string('stream_port')->after('name');
            $table->string('stream_ip')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_streams', function(Blueprint $table)
        {
            $table->string('url')->after('name');
            $table->dropColumn('stream_ip');
            $table->dropColumn('stream_port');
            $table->dropColumn('stream_app');
        });
    }
}
