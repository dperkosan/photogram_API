<?php

namespace App\Functional\Api\V1\Controllers;

use DB;
use App\TestCase;
use Carbon\Carbon;

class ResetPasswordControllerTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        DB::table('password_resets')->insert([
            'email' => $this->getTestUserEmail(),
            'token' => 'my_super_secret_code',
            'created_at' => Carbon::now()
        ]);
    }

    public function testResetSuccessfully()
    {
        $res = $this->post('api/auth/reset', $this->getResetPasswordData());

        echo $res->getContent();

        $res->assertStatus(201);
    }

    public function testResetReturnsProcessError()
    {
        $data = array_merge($this->getResetPasswordData(), ['token' => 'this_code_is_invalid']);

        $this->post('api/auth/reset', $data)
          ->assertStatus(500);
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
          'token' => 'my_super_secret_code',
          'password' => 'mynewpass',
          'password_confirmation' => 'mynewpass'
        ];
    }

}
