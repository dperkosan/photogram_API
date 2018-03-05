<?php

namespace App\Functional\Api\V1\Controllers;

use App\TestCase;

class ResetPasswordControllerTest extends TestCase
{
    protected $testToken;

    protected function setUp()
    {
        parent::setUp();

        $broker = \Password::broker();

        $user = $broker->getUser(['email' => $this->getTestUserEmail()]);

        $this->testToken = $broker->createToken($user);
    }

    public function testResetSuccessfully()
    {
        $res = $this->post('api/auth/reset', $this->getResetPasswordData());

        echo $res->getContent();

        $res->assertStatus(204);

        $updateArray = [
          'password' => bcrypt($this->getTestUserData()['password'])
        ];

        \DB::table('users')->where('email', '=', $this->getTestUserEmail())
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
          'email' => $this->getTestUserEmail(),
          'token' => $this->testToken,
          'password' => 'mynewpass',
          'password_confirmation' => 'mynewpass'
        ];
    }

}
