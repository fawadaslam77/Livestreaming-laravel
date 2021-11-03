<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToStreamUserTagsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('stream_user_tags', function(Blueprint $table)
		{
			$table->foreign('stream_id', 'stream_tag_streams_rel')->references('id')->on('user_streams')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('user_id', 'stream_tag_user_rel')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('stream_user_tags', function(Blueprint $table)
		{
			$table->dropForeign('stream_tag_streams_rel');
			$table->dropForeign('stream_tag_user_rel');
		});
	}

}
