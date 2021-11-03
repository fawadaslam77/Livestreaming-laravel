<?php

namespace Tests\Unit\Api;

use App\Models\Package;
use Tests\TestCase;
use \Illuminate\Http\UploadedFile as SymfonyUploadedFile;

class PackagesCrudTest extends TestCase
{
    protected $package;

    protected function addPackage()
    {
        $data = [
            'name' => 'Free Package',
            'daily_limit' => 2,
            'storage_limit' => 2048,
            'expire_days' => -1,
            'dashboard' => 0,
            'allow_240' => 1,
            'allow_480' => 1,
            'allow_720' => 0,
            'allow_1080' => 0,
            'allow_save_offline' => 0,
            'disable_ads' => 0,
        ];
        $this->package = Package::create($data);
        return $this->package;
    }
    public function testPackageIndex()
    {
        $this->addPackage();
        $response = $this->getJson("/api/packages", [
            'token' => $this->appUserLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json);
        // TODO: Check Package Array
    }

    public function testPackageView()
    {
        $package = $this->addPackage();

        $response = $this->getJson("/api/packages/". $package->id, [
            'token' => $this->appUserLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json, 202);
        // TODO: Check Package Array
    }

    public function testPackageCreate()
    {
        $data = [
            'name' => 'Free Package',
            'daily_limit' => 2,
            'storage_limit' => 2048,
            'expire_days' => -1,
            'dashboard' => 0,
            'allow_240' => 1,
            'allow_480' => 1,
            'allow_720' => 0,
            'allow_1080' => 0,
            'allow_save_offline' => 0,
            'disable_ads' => 0,
        ];
        $response = $this->postJson("/api/packages", $data, [
            'token' => $this->adminLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json,201);
        // TODO: Check Package Array
        $this->assertDatabaseHas('packages', $data);
    }


    public function testPackageUpdate()
    {
        $package = $this->addPackage();
        $data = [
            'name' => 'Silver Package',
            'daily_limit' => 5,
            'storage_limit' => 5120,
            'expire_days' => 365,
            'dashboard' => 0,
            'allow_240' => 1,
            'allow_480' => 1,
            'allow_720' => 1,
            'allow_1080' => 1,
            'allow_save_offline' => 0,
            'disable_ads' => 0,
        ];
        $response = $this->postJson("/api/packages/" . $package->id, ['_method'=>'PATCH'] + $data, [
            'token' => $this->adminLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json,202);
        $data['id'] = $package->id;
        $this->assertDatabaseHas('packages', $data);
    }

    public function testPackageDelete()
    {
        $package = $this->addPackage();
        $response = $this->deleteJson("/api/packages/" . $package->id, [
            'token' => $this->adminLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json,202);
        $this->assertDatabaseMissing('packages', [
            'id'=> $package->id,
            'deleted_at' => null
        ]);
    }
}
