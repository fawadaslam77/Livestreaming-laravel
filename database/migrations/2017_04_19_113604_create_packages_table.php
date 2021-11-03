<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePackagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('packages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->nullable();
			$table->integer('daily_limit')->nullable();
			$table->integer('storage_limit')->nullable();
			$table->integer('expire_days')->nullable();
			$table->boolean('dashboard')->nullable();
			$table->boolean('allow_240')->nullable();
			$table->boolean('allow_480')->nullable();
			$table->boolean('allow_720')->nullable();
			$table->boolean('allow_1080')->nullable();
			$table->boolean('allow_save_offline')->nullable();
			$table->boolean('disable_ads')->nullable();
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
		Schema::drop('packages');
	}

}
