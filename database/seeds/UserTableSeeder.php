<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'role_id' => 1,
            'first_name' => 'Guest',
            'last_name' => 'User',
            'full_name' => 'Guest User',
            'username' => 'guestuser',
            'email' => 'guest@example.com',
            'password' => '0987654321',
            'status' => 1,
            'is_verified' => 1,
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);
        DB::table('users')->insert([
            'role_id' => 3,
            'first_name' => 'Admin',
            'full_name' => 'Administrator',
            'username' => 'administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'status' => 1,
            'is_verified' => 1,
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);
        DB::table('users')->insert([
            'role_id' => 2,
            'first_name' => 'App',
            'last_name' => 'User',
            'full_name' => 'App User ',
            'username' => 'app-user',
            'email' => 'app-user@example.com',
            'password' => bcrypt('password'),
            'status' => 1,
            'is_verified' => 1,
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);
    }
}
