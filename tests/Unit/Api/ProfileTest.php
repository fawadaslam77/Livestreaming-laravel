<?php

namespace Tests\Unit\Api;

use Tests\TestCase;
use \Illuminate\Http\UploadedFile as SymfonyUploadedFile;

class ProfileTest extends TestCase
{
    public function testChangePassword()
    {
        $newUserToken = $this->addUser();
        $newPassword = 'newpassword';
        $response = $this->postJson("/api/change-password", [
            'user_id' => $this->user->id,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ], [
            'token'=> $newUserToken
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json);
        $userData = [
            'email'=>$this->user->email,
            'password'=>$newPassword
        ];
        $user = \JWTAuth::toUser(\JWTAuth::attempt($userData));
        $this->assertEquals($this->user->id , $user->id);
        // TODO: Check User Array
    }

    public function testGetUser()
    {
        $newUserToken = $this->addUser();

        $response = $this->getJson("/api/user", [
            'token'=> $newUserToken
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json);
        // TODO: Check User Array
    }

    public function testUpdateDevice()
    {
        $newUserToken = $this->addUser();
        $deviceToken = "xT0xskdj2sk39E";
        $deviceType = 'ios';
        $response = $this->postJson("/api/device-info", [
            '_method' => 'PATCH',
            'user_id' => $this->user->id,
            'device_token' => $deviceToken,
            'device_type' => $deviceType,
        ],[
            'token'=> $newUserToken
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json);
        $this->assertDatabaseHas('users', [
            'id'=> $this->user->id,
            'device_token' => $deviceToken,
            'device_type' => $deviceType,
        ]);
        // TODO: Check User Array
    }

    public function testNotificationStatus()
    {
        $newUserToken = $this->addUser();
        $response = $this->postJson("/api/notification-status", [
            '_method' => 'PATCH',
            'user_id' => $this->user->id,
            'status' => 0,
        ],[
            'token'=> $newUserToken
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json);
        $this->assertDatabaseHas('users', [
            'id'=> $this->user->id,
            'notification_status' => 0,
        ]);
        // TODO: Check User Array
    }

    public function testUpdateUserWithoutPassword()
    {
        $newUserToken = $this->addUser();
        $fullname = "new name";
        $phoneNumber = "0800111282";
        $response = $this->postJson("/api/user", [
            '_method' => 'PATCH',
            'user_id' => $this->user->id,
            'full_name' => $fullname,
            'mobile_no' => $phoneNumber,
        ],[
            'token'=> $newUserToken
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json);
        $this->assertDatabaseHas('users', [
            'id'=> $this->user->id,
            'full_name' => $fullname,
            'mobile_no' => $phoneNumber,
        ]);
        // TODO: Check User Array
    }

    public function testUpdateUserWithPassword()
    {
        $newUserToken = $this->addUser();
        $fullname = "new name";
        $phoneNumber = "0800111282";
        $password  = "password1234567890";
        $response = $this->postJson("/api/user", [
            '_method' => 'PATCH',
            'user_id' => $this->user->id,
            'full_name' => $fullname,
            'mobile_no' => $phoneNumber,
            'old_password' => $this->userPassword,
            'password' => $password,
            'password_confirmation' => $password,
        ],[
            'token'=> $newUserToken
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json);
        $userData = [
            'email'=>$this->user->email,
            'password'=>$password
        ];
        $user = \JWTAuth::toUser(\JWTAuth::attempt($userData));
        $this->assertEquals($this->user->id , $user->id);
        $this->assertEquals($fullname , $user->full_name);
        $this->assertEquals($phoneNumber , $user->mobile_no);
        // TODO: Check User Array
    }

    // TODO: Add testUpdateUserWithPicture
}
