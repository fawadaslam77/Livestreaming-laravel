<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFriendRequestMediumsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('friend_request_mediums', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('friend_requests_id')->unsigned()->nullable()->index('friend_request_medium_friend_rel');
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
		Schema::drop('friend_request_mediums');
	}

}
