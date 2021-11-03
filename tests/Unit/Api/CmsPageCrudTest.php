<?php

namespace Tests\Unit\Api;

use App\Models\CmsPage;
use Tests\TestCase;
use \Illuminate\Http\UploadedFile as SymfonyUploadedFile;

class CmsPageTest extends TestCase
{
    protected $cmsPage;

    protected function addPage()
    {
        $data = [
            'name' => 'First Page',
            'title' => 'First Page',
            'body' => 'This is first page of cms',
        ];
        $this->cmsPage = CmsPage::create($data);
        return $this->cmsPage;
    }
    public function testCmsPageIndex()
    {
        $this->addPage();
        $response = $this->getJson("/api/cms-pages", [
            'token' => $this->appUserLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json);
        // TODO: Check Page Array
    }

    public function testCmsPageView()
    {
        $page = $this->addPage();

        $response = $this->getJson("/api/cms-pages/". $page->id, [
            'token' => $this->appUserLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json, 202);
        // TODO: Check Page Array
    }

    public function testCmsPageCreate()
    {
        $name = 'Page Name';
        $title = 'Page Title';
        $body = 'Page Body';
        $response = $this->postJson("/api/cms-pages", [
            'name' => $name,
            'title' => $title,
            'body' => $body,
        ], [
            'token' => $this->adminLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json,201);
        // TODO: Check Page Array

        $this->assertDatabaseHas('cms_pages', [
            'name' => $name,
            'title' => $title,
            'body' => $body,
        ]);
    }


    public function testCmsPageUpdate()
    {
        $page = $this->addPage();
        $name = 'New Page Name';
        $title = 'New Page Title';
        $body = 'New Page Body';
        $response = $this->postJson("/api/cms-pages/" . $page->id, [
            '_method' => 'PATCH',
            'name' => $name,
            'title' => $title,
            'body' => $body,
        ], [
            'token' => $this->adminLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json,202);
        $this->assertDatabaseHas('cms_pages', [
            'id'=> $page->id,
            'name' => $name,
            'title' => $title,
            'body' => $body,
        ]);
    }

    public function testCmsPageDelete()
    {
        $page = $this->addPage();
        $response = $this->deleteJson("/api/cms-pages/" . $page->id, [
            'token' => $this->adminLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json,202);
        $this->assertDatabaseMissing('cms_pages', [
            'id'=> $page->id,
            'name'=> $page->name,
            'title'=> $page->title,
            'body'=> $page->body,
            'deleted_at' => null
        ]);
    }
}
