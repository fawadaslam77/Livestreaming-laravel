<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StreamCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stream_categories')->insert([
            'name' => 'News',
            'description' => 'News',
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);
        DB::table('stream_categories')->insert([
            'name' => 'Entertainment',
            'description' => 'Entertainment',
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);
        DB::table('stream_categories')->insert([
            'name' => 'Education',
            'description' => 'Education',
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);
        DB::table('stream_categories')->insert([
            'name' => 'Sports',
            'description' => 'Sports',
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);
        DB::table('stream_categories')->insert([
            'name' => 'Technology',
            'description' => 'Technology',
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);
        DB::table('stream_categories')->insert([
            'name' => 'Food &amp; Restaurants',
            'description' => 'Food &amp; Restaurants',
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);
    }
}
