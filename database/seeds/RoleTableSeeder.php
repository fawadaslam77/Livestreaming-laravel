<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'name' => 'Guest',
            'slug' => 'guest',
            'is_admin' => 0,
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);
        DB::table('roles')->insert([
            'name' => 'App User',
            'slug' => 'app-user',
            'is_admin' => 0,
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);
        DB::table('roles')->insert([
            'name' => 'Administrator',
            'slug' => 'administrator',
            'is_admin' => 1,
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);
    }
}
