<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToUserPackagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_packages', function(Blueprint $table)
		{
			$table->foreign('package_id', 'user_packages_package_rel')->references('id')->on('packages')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('user_id', 'user_packages_user_rel')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_packages', function(Blueprint $table)
		{
			$table->dropForeign('user_packages_package_rel');
			$table->dropForeign('user_packages_user_rel');
		});
	}

}
