<?php

namespace Tests\Unit\Api;

use Tests\TestCase;
use \Illuminate\Http\UploadedFile as SymfonyUploadedFile;

class AuthenticationTest extends TestCase
{

    public function testGuestToken()
    {
        $response = $this->postJson("/api/guest-token", [
            'project_name' => 'Streamix',
        ]);
        $json = $response->decodeResponseJson();
        if($this->checkResponseCode($json)){
            $this->assertEquals('U3RyZWFtaXg=', $json['Result']['token']);
        }
    }

    public function testRegister()
    {
        $response = $this->postJson("/api/register", [
            'email' => 'new-user@example.com',
            'password' => 'password',
            'full_name' => 'new user',
        ]);
        $json = $response->decodeResponseJson();
        if($this->checkResponseCode($json)){
            // TODO: Check User Array
            return $json['Result']['user']['token'];
        }
        return "";
    }

    public function testCheckUsername()
    {
        $newUserToken = $this->addUser();

        $response = $this->postJson("/api/username-availability", [
            'username' => 'newuser'
        ], [
            'token'=> $newUserToken
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json);
    }

    public function testRegisterStep3()
    {
        $newUserToken = $this->addUser();

        $path = __DIR__.'/../../../public/images-profile/';
        $local_file = $path . 'default.jpg';
        $newFile    = $path . 'copy.jpg';
        copy($local_file, $newFile);
        $uploadedFile = new SymfonyUploadedFile( $newFile, 'avatar.jpg', 'image/jpeg', null, null, true );

        $response = $this->postJson("/api/register", [
            '_method' => 'PATCH',
            'username' => 'newuser',
            'user_id' => '4',
            'profile_picture' => $uploadedFile,
        ], [
            'token'=> $newUserToken
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json);
        // TODO: Check User Array
    }

    public function testForgotPassword()
    {
        $newUserToken = $this->addUser();

        $response = $this->postJson("/api/forgot-password", [
            'email' => 'new-user@example.com',
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json);
    }

    public function testLogin()
    {
        $response = $this->postJson("/api/login", [
            'email' => 'app-user@example.com',
            'password'=> 'password',
            'role_id' => 2,
        ]);
        $json = $response->decodeResponseJson();
        if($this->checkResponseCode($json)){
            // TODO: Check User Array
            return $json['Result']['user']['token'];
        }
        return "";
    }

    public function testAdminLogin()
    {
        $response = $this->postJson("/api/login", [
            'email' => 'admin@example.com',
            'password'=> 'password',
            'role_id' => 3,
        ]);
        $json = $response->decodeResponseJson();
        if($this->checkResponseCode($json)){
            // TODO: Check User Array
            return $json['Result']['user']['token'];
        }
        return "";
    }

    public function testRenewToken(){
        $response = $this->postJson("/api/renew-token", [
            'user_id' => 3,
            'email' => 'app-user@example.com',
            //'role_id' => 2,
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json);
        // TODO: Check User Array
    }

    public function testRenewAdminToken(){
        $response = $this->postJson("/api/renew-token", [
            'user_id' => 2,
            'email' => 'admin@example.com',
            'role_id' => 3,
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json);
        // TODO: Check User Array
    }

    /**
     * @depends testAdminLogin
     */
    public function testAdminLogout($adminToken)
    {
        $response = $this->postJson("/api/logout", [], [
            'token'=> $adminToken
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json);
    }

    /**
     * @depends testLogin
     */
    public function testLogout($userToken)
    {
        $response = $this->postJson("/api/logout", [], [
            'token'=> $userToken
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json);
    }
}
