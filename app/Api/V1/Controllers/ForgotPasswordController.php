<?php

namespace App\Api\V1\Controllers;

use Illuminate\Support\Facades\Password;
use App\Api\V1\Requests\ForgotPasswordRequest;
use App\Interfaces\UserRepositoryInterface;

class ForgotPasswordController extends ApiController
{
    /**
     * @var UserRepositoryInterface
     */
    private $user;
    
    public function __construct(UserRepositoryInterface $user)
    {
        $this->user = $user;
    }
    
    public function sendResetEmail(ForgotPasswordRequest $request)
    {
        //get user by email
        $emailExists = $this->user->emailExists($request->get('email'));

        if(!$emailExists) {
            return $this->respondWrongArgs();
        }

        $broker = $this->getPasswordBroker();
        $sendingResponse = $broker->sendResetLink($request->only('email'));

        if($sendingResponse !== Password::RESET_LINK_SENT) {
            return $this->respondInternalError();
        }

        return $this->respondSuccess();
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    private function getPasswordBroker()
    {
        return Password::broker();
    }
}
