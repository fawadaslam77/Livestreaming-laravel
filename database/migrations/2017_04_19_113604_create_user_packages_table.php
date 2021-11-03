<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserPackagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_packages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->nullable()->index('user_packages_user_rel');
			$table->integer('package_id')->unsigned()->nullable()->index('user_packages_package_rel');
			$table->float('amount', 10)->nullable();
			$table->integer('payment_status')->nullable();
			$table->text('payment_response', 65535)->nullable();
			$table->dateTime('expire_at')->nullable();
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
		Schema::drop('user_packages');
	}

}
