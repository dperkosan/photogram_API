<?php

namespace App\Functional\Api\V1\Controllers;

use App\TestCase;

class LoginControllerTest extends TestCase
{
    public function testLoginSuccessfully()
    {
        $res = $this->call('POST', 'api/auth/login', [
            'email'    => $this->getTestUserEmail(),
            'password' => $this->getTestUserData()['password'],
        ]);

        $responseJson = $res->decodeResponseJson();

        $res->assertJsonStructure(['token'], $responseJson)
            ->assertSuccessful();

        return $responseJson['token'];
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
            'email' => $this->getTestUserEmail(),
        ])->assertStatus(422);
    }
}
