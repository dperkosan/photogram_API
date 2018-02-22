<?php

namespace App\Functional\Api\V1\Controllers;

use Config;
use App\TestCase;

class SignUpControllerTest extends TestCase
{
//    use DatabaseMigrations;

    protected $signUpUser = [
      'email'                 => 'blinktest@example.com',
      'name'                  => 'Blink Test',
      'username'              => 'blinktest',
      'password'              => 'blinktest',
      'password_confirmation' => 'blinktest',
    ];

    public function testSignUpSuccessfully()
    {
        $res = $this->post('api/auth/signup', $this->signUpUser);

//        echo $res->getContent();

        $res->assertStatus(201);

        \DB::table('users')->where('email', '=', $this->signUpUser['email'])->delete();
    }

    public function testSignUpReturnsValidationError()
    {
        $this->post('api/auth/signup', array_merge($this->signUpUser, ['password_confirmation' => 'different']))
          ->assertJsonStructure(['error'])
          ->assertStatus(422);
    }
}
