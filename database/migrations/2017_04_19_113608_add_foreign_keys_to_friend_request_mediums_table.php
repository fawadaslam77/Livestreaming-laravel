<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToFriendRequestMediumsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('friend_request_mediums', function(Blueprint $table)
		{
			$table->foreign('friend_requests_id', 'friend_request_medium_friend_request_rel')->references('id')->on('user_friend_requests')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('friend_request_mediums', function(Blueprint $table)
		{
			$table->dropForeign('friend_request_medium_friend_request_rel');
		});
	}

}
