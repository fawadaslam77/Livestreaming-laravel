<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $user;
    protected $userPassword = "abc123xyz890";

    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
        $this->seed('DatabaseSeeder');
    }

    public function tearDown()
    {
        Artisan::call('migrate:reset');
        parent::tearDown();
    }

    protected function addUser() {
        $data = [
            'role_id' => 2,
            'first_name' => 'New',
            'last_name' => 'User',
            'full_name' => 'New User',
            'username' => 'new-user',
            'email' => 'new-user@example.com',
            'password' => bcrypt($this->userPassword),
            'is_verified' => 1,
        ];
        $this->user = User::create($data);
        return \JWTAuth::fromUser($this->user);
    }

    protected function appUserLogin() {
        $user = User::query()->where('role_id',2)->first();
        return \JWTAuth::fromUser($user);
    }

    protected function adminLogin() {
        $user = User::query()->where('role_id',3)->first();
        return \JWTAuth::fromUser($user);
    }

    protected function checkResponseCode($response, $code=200){
        $this->assertArrayHasKey('Response', $response); // Fail! if Response is not found in the array.
        $this->assertEquals($code,$response['Response'], $response['Message'] . " Result: " . json_encode($response['Result'])); // Fail! if response != 200
        return (isset($response['Response']) && $response['Response'] == $code);
    }
}
