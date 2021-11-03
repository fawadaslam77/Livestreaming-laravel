<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSocialAccountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('social_accounts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->nullable()->index('user_social_account');
			$table->string('platform')->nullable();
			$table->string('client_id')->nullable();
			$table->text('properties', 65535)->nullable();
			$table->string('token')->nullable();
			$table->string('code', 32)->nullable();
			$table->string('email')->nullable();
			$table->string('username')->nullable();
			$table->dateTime('expires_at')->nullable();
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
		Schema::drop('social_accounts');
	}

}
