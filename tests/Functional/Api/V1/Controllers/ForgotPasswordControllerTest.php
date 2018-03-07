<?php

namespace App\Functional\Api\V1\Controllers;

use App\DataProvider;
use App\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    public function testForgotPasswordRecoverySuccessfully()
    {
        $this->post('api/auth/recovery', [
            'email' => DataProvider::getTestUserEmail()
        ])->assertSuccessful();
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
