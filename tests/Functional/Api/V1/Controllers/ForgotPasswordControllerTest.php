<?php

namespace App\Functional\Api\V1\Controllers;

use App\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    public function testForgotPasswordRecoverySuccessfully()
    {
        $res = $this->post('api/auth/recovery', [
            'email' => $this->getTestUserEmail()
        ]);

//        echo $res->getContent();

        $res->assertSuccessful();
    }

    public function testForgotPasswordRecoveryReturnsUserNotFoundError()
    {
        $this->post('api/auth/recovery', [
            'email' => 'unknown@email.com'
        ])->assertStatus(422);
    }

    public function testForgotPasswordRecoveryReturnsValidationErrors()
    {
        $this->post('api/auth/recovery', [
            'email' => 'i am not an email'
        ])->assertStatus(422);
    }
}
