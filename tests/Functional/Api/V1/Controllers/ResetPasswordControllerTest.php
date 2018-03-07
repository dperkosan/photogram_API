<?php

namespace App\Functional\Api\V1\Controllers;

use App\DataProvider;
use App\TestCase;

class ResetPasswordControllerTest extends TestCase
{
    protected $testToken;

    protected function setUp()
    {
        parent::setUp();

        $broker = \Password::broker();

        $user = $broker->getUser(['email' => DataProvider::getTestUserEmail()]);

        $this->testToken = $broker->createToken($user);
    }

    public function testResetSuccessfully()
    {
        $res = $this->post('api/auth/reset', $this->getResetPasswordData());

        echo $res->getContent();

        $res->assertStatus(204);

        $updateArray = [
          'password' => bcrypt(DataProvider::getTestUserData()['password'])
        ];

        \DB::table('users')->where('email', '=', DataProvider::getTestUserEmail())
          ->update($updateArray);
    }

    public function testResetReturnsProcessError()
    {
        $data = array_merge($this->getResetPasswordData(), ['token' => 'this_token_is_invalid']);

        $this->post('api/auth/reset', $data)
          ->assertStatus(403);
    }

    public function testResetReturnsValidationError()
    {
        $data = array_merge($this->getResetPasswordData(), ['password_confirmation' => 'different']);

        $this->post('api/auth/reset', $data)
            ->assertStatus(422);
    }

    // helpers

    protected function getResetPasswordData()
    {
        return [
          'email' => DataProvider::getTestUserEmail(),
          'token' => $this->testToken,
          'password' => 'mynewpass',
          'password_confirmation' => 'mynewpass'
        ];
    }

}
