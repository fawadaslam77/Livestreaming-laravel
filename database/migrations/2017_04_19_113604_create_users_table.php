<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('role_id')->unsigned()->index('user_role');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('full_name', 150)->default('');
            $table->string('username')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('status')->default(1)->comment('1 means active , 0 means blocked/inactive');
            $table->string('status_text')->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', array('male','female'))->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('mobile_no', 100)->nullable();
            $table->string('profile_picture')->nullable();
            $table->integer('privacy_setting')->nullable()->default(40);
            $table->boolean('notification_status')->default(1);
            $table->string('device_type', 100)->default('');
            $table->string('device_token')->default('');
            $table->string('verification_code', 6)->default('');
            $table->boolean('is_verified')->default(1)->comment('1 means verified , 0 means unverified');
            $table->boolean('is_subadmin')->default(0);
            $table->boolean('is_available')->default(1);
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('last_login')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }

}
