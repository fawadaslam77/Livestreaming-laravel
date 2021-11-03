<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'config_key' => 'core.application',
            'config_value' => 'Streamix',
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);
        DB::table('settings')->insert([
            'config_key' => 'core.version',
            'config_value' => 1.0,
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);
        DB::table('settings')->insert([
            'config_key' => 'email.support',
            'config_value' => 'support@example.com',
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);
        DB::table('settings')->insert([
            'config_key' => 'email.contact',
            'config_value' => 'contact@example.com',
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);
        DB::table('settings')->insert([
            'config_key' => 'app.link.tutorial_video',
            'config_value' => 'http://www.example.com/dummy_video.mp4',
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);
        DB::table('settings')->insert([
            'config_key' => 'app.link.guide_book',
            'config_value' => 'http://www.example.com/dummy_doc.pdf',
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);
        DB::table('settings')->insert([
            'config_key' => 'app.default.user_role',
            'config_value' => 2,
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);
    }
}
