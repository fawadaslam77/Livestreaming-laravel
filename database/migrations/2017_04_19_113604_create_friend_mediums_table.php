<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFriendMediumsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('friend_mediums', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('friends_id')->unsigned()->nullable()->index('friend_medium_friend_rel');
			$table->integer('medium')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('friend_mediums');
	}

}
