<?php

namespace App\Functional\Api\V1\Controllers;

use Hash;
use App\User;
use App\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginControllerTest extends TestCase
{
//    public function setUp()
//    {
//        parent::setUp();
//
//        $user = new User([
//            'name' => 'Test',
//            'email' => 'test@email.com',
//            'password' => '123456'
//        ]);
//
//        $user->save();
//    }

    public function testLoginSuccessfully()
    {
        $this->call('POST', 'api/auth/login', [
            'email' => 'uroshcs@gmail.com',
            'password' => 'urosadmin'
        ])->assertSuccessful();
    }

    public function testLoginWithReturnsWrongCredentialsError()
    {
        $this->post('api/auth/login', [
            'email' => 'unknown@email.com',
            'password' => '123456'
        ])->seeJsonStructure([
          'success' => false
        ])->assertResponseStatus(403);
    }

    public function testLoginWithReturnsValidationError()
    {
        $this->post('api/auth/login', [
            'email' => 'test@email.com'
        ])->seeJsonStructure([
          'success' => false
        ])->assertResponseStatus(422);
    }
}
