<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserStreamsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_streams', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->nullable()->index('user_streams_user_rel');
			$table->string('uuid')->nullable();
			$table->string('name')->nullable();
			$table->integer('status')->nullable();
			$table->integer('privacy_setting')->nullable();
			$table->integer('quality')->nullable();
			$table->boolean('is_public')->nullable()->default(1);
			$table->boolean('allow_comments')->nullable()->default(1);
			$table->boolean('allow_tag_requests')->nullable()->default(1);
			$table->boolean('available_later')->nullable()->default(1);
			$table->float('lng', 10, 6)->nullable();
			$table->float('lat', 10, 6)->nullable();
			$table->dateTime('start_time')->nullable();
			$table->integer('total_likes')->nullable();
			$table->integer('total_dislikes')->nullable();
			$table->integer('total_shares')->nullable()->default(0);
			$table->integer('total_viewers')->unsigned()->nullable()->default(0);
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
		Schema::drop('user_streams');
	}

}
