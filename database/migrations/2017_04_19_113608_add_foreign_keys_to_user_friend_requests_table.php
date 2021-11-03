<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToUserFriendRequestsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_friend_requests', function(Blueprint $table)
		{
			$table->foreign('friend_user_id', 'user_friend_request_friend_rel')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('user_id', 'user_friend_request_user_rel')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_friend_requests', function(Blueprint $table)
		{
			$table->dropForeign('user_friend_request_friend_rel');
			$table->dropForeign('user_friend_request_user_rel');
		});
	}

}
