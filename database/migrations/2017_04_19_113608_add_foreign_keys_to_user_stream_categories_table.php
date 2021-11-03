<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToUserStreamCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_stream_categories', function(Blueprint $table)
		{
			$table->foreign('category_id', 'user_stream_category_category_rel')->references('id')->on('stream_categories')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('stream_id', 'user_stream_category_stream_rel')->references('id')->on('user_streams')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_stream_categories', function(Blueprint $table)
		{
			$table->dropForeign('user_stream_category_category_rel');
			$table->dropForeign('user_stream_category_stream_rel');
		});
	}

}
