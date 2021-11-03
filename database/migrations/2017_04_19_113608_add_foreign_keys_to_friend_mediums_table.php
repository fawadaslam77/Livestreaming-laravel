<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToFriendMediumsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('friend_mediums', function(Blueprint $table)
		{
			$table->foreign('friends_id', 'friend_medium_friend_rel')->references('id')->on('friends')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('friend_mediums', function(Blueprint $table)
		{
			$table->dropForeign('friend_medium_friend_rel');
		});
	}

}
