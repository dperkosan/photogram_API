<?php

namespace App\Functional\Api\V1\Controllers;

use App\DataProvider;
use App\TestCase;

class LoginControllerTest extends TestCase
{
    public function testLoginSuccessfully()
    {
        $this->call('POST', 'api/auth/login', [
            'email'    => DataProvider::getTestUserEmail(),
            'password' => DataProvider::getTestUserData()['password'],
        ])
            ->assertJsonStructure(['token'])
            ->assertSuccessful();
    }

    public function testLoginWithReturnsWrongCredentialsError()
    {
        $this->post('api/auth/login', [
            'email'    => 'unknown@email.com',
            'password' => '123456',
        ])->assertStatus(403);
    }

    public function testLoginWithReturnsValidationError()
    {
        $this->post('api/auth/login', [
            'email' => DataProvider::getTestUserEmail(),
        ])->assertStatus(422);
    }
}
