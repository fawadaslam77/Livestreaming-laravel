<?php

namespace Tests\Unit\Api;

use App\Models\Role;
use Tests\TestCase;
use \Illuminate\Http\UploadedFile as SymfonyUploadedFile;

class RolesCrudTest extends TestCase
{
    protected $role;

    protected function addRole()
    {
        $data = [
            'name' => 'Role Name',
        ];
        $this->role = Role::create($data);
        return $this->role;
    }
    public function testRoleIndex()
    {
        $this->addRole();
        $response = $this->getJson("/api/roles", [
            'token' => $this->adminLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json);
        // TODO: Check Role Array
    }

    public function testRoleView()
    {
        $role = $this->addRole();

        $response = $this->getJson("/api/roles/". $role->id, [
            'token' => $this->adminLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json, 202);
        // TODO: Check Role Array
    }

    public function testRoleCreate()
    {
        $data = [
            'name' => 'Role Name',
        ];
        $response = $this->postJson("/api/roles", $data, [
            'token' => $this->adminLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json,201);
        // TODO: Check Role Array
        $this->assertDatabaseHas('roles', $data);
    }


    public function testRoleUpdate()
    {
        $role = $this->addRole();
        $data = [
            'name' => 'New Role Name',
        ];
        $response = $this->postJson("/api/roles/" . $role->id, ['_method'=>'PATCH'] + $data, [
            'token' => $this->adminLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json,202);
        $data['id'] = $role->id;
        $this->assertDatabaseHas('roles', $data);
    }

    public function testRoleDelete()
    {
        $role = $this->addRole();
        $response = $this->deleteJson("/api/roles/" . $role->id, [
            'token' => $this->adminLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json,202);
        $this->assertDatabaseMissing('roles', [
            'id'=> $role->id,
            'deleted_at' => null
        ]);
    }
}
