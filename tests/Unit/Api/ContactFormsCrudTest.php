<?php

namespace Tests\Unit\Api;

use App\Models\ContactForm;
use Tests\TestCase;
use \Illuminate\Http\UploadedFile as SymfonyUploadedFile;

class ContactFormsCrudTest extends TestCase
{
    protected $contactForm;

    protected function addContactForm()
    {
        $data = [
            'user_id' => 2,
            'email' => 'user@example.com',
            'comments' => 'Contact Form Comment',
        ];
        $this->contactForm = ContactForm::create($data);
        return $this->contactForm;
    }
    public function testContactFormIndex()
    {
        $this->addContactForm();
        $response = $this->getJson("/api/contact-forms", [
            'token' => $this->adminLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json);
        // TODO: Check Contact Form Array
    }

    public function testContactFormView()
    {
        $form = $this->addContactForm();

        $response = $this->getJson("/api/contact-forms/". $form->id, [
            'token' => $this->adminLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json, 202);
        // TODO: Check Contact Form Array
    }

    public function testContactFormCreate()
    {
        $user_id = 2;
        $email = 'user@example.com';
        $comments = 'Contact Form Comment';
        $response = $this->postJson("/api/contact-forms", [
            'user_id' => $user_id,
            'email' => $email,
            'comments' => $comments,
        ], [
            'token' => $this->appUserLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json,201);
        // TODO: Check Contact Form Array

        $this->assertDatabaseHas('contact_forms', [
            'user_id' => $user_id,
            'email' => $email,
            'comments' => $comments,
        ]);
    }


    public function testContactFormUpdate()
    {
        $form = $this->addContactForm();
        $email = 'user2@example.com';
        $comments = 'Contact Form New Comment';
        $response = $this->postJson("/api/contact-forms/" . $form->id, [
            '_method' => 'PATCH',
            'email' => $email,
            'comments' => $comments,
        ], [
            'token' => $this->adminLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json,202);
        $this->assertDatabaseHas('contact_forms', [
            'id'=> $form->id,
            'email' => $email,
            'comments' => $comments,
        ]);
    }

    public function testContactFormDelete()
    {
        $form = $this->addContactForm();
        $response = $this->deleteJson("/api/contact-forms/" . $form->id, [
            'token' => $this->adminLogin()
        ]);
        $json = $response->decodeResponseJson();
        $this->checkResponseCode($json,202);
        $this->assertDatabaseMissing('contact_forms', [
            'id'=> $form->id,
            'email'=> $form->email,
            'comments'=> $form->comments,
            'deleted_at' => null
        ]);
    }
}
