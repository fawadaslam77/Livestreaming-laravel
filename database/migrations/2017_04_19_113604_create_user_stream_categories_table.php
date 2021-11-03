<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserStreamCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_stream_categories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('stream_id')->unsigned()->nullable()->index('user_stream_category_stream_rel');
			$table->integer('category_id')->unsigned()->nullable()->index('user_stream_category_category_rel');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_stream_categories');
	}

}
