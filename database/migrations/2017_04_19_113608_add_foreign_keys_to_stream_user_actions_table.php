<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToStreamUserActionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('stream_user_actions', function(Blueprint $table)
		{
			$table->foreign('stream_id', 'stream_user_action_stream_rel')->references('id')->on('user_streams')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('user_id', 'stream_user_action_uses_rel')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('stream_user_actions', function(Blueprint $table)
		{
			$table->dropForeign('stream_user_action_stream_rel');
			$table->dropForeign('stream_user_action_uses_rel');
		});
	}

}
