<?php

namespace Tests\Unit\Api;

use App\Models\StreamCategory;
use Tests\TestCase;
use \Illuminate\Http\UploadedFile as SymfonyUploadedFile;

class StreamCategoriesCrudTest extends TestCase
{
    protected $streamCategory;

    protected function addStreamCategory()
    {
        $data = [
            'name' => 'Stream Category Name',
            'description' => 'Stream Category Description',
        ];
        $this->streamCategory = StreamCategory::create($data);
        return $this->streamCategory;
    }
    public function testStreamCategoryIndex()
    {
        $this->addStreamCategory();
        $response = $this->getJson("/api/stream-categories", [
            'token' => $this->appUserLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json);
        // TODO: Check Stream Category Array
    }

    public function testStreamCategoryView()
    {
        $streamCategory = $this->addStreamCategory();

        $response = $this->getJson("/api/stream-categories/". $streamCategory->id, [
            'token' => $this->appUserLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json, 202);
        // TODO: Check Stream Category Array
    }

    public function testStreamCategoryCreate()
    {
        $data = [
            'name' => 'Stream Category Name',
            'description' => 'Stream Category Description',
        ];
        $response = $this->postJson("/api/stream-categories", $data, [
            'token' => $this->adminLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json,201);
        // TODO: Check Stream Category Array
        $this->assertDatabaseHas('stream_categories', $data);
    }


    public function testStreamCategoryUpdate()
    {
        $streamCategory = $this->addStreamCategory();
        $data = [
            'name' => 'New Stream Category Name',
            'description' => 'New Stream Category Description',
        ];
        $response = $this->postJson("/api/stream-categories/" . $streamCategory->id, ['_method'=>'PATCH'] + $data, [
            'token' => $this->adminLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json,202);
        $data['id'] = $streamCategory->id;
        $this->assertDatabaseHas('stream_categories', $data);
    }

    public function testStreamCategoryDelete()
    {
        $streamCategory = $this->addStreamCategory();
        $response = $this->deleteJson("/api/stream-categories/" . $streamCategory->id, [
            'token' => $this->adminLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json,202);
        $this->assertDatabaseMissing('stream_categories', [
            'id'=> $streamCategory->id,
            'deleted_at' => null
        ]);
    }
}
