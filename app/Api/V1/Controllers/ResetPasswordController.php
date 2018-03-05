<?php

namespace App\Api\V1\Controllers;

use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Password;
use App\Interfaces\UserRepositoryInterface;
use App\Api\V1\Requests\ResetPasswordRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ResetPasswordController extends ApiController
{
    /**
     * @var UserRepositoryInterface
     */
    protected $user;

    public function __construct(UserRepositoryInterface $user)
    {
        $this->user = $user;
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $broker = $this->broker();

        // We are already checking if password is confirmed in the ResetPasswordRequest
        // so we are setting the broker validator to just return true
        $broker->validator(function ($credentials) {
            return true;
        });
        $response = $broker->reset(
            $this->credentials($request), function ($user, $password) {
                $this->reset($user, $password);
            }
        );

        if ($response === Password::INVALID_USER) {
            return $this->respondForbidden('This email is not registered.');
        } else if ($response === Password::INVALID_TOKEN) {
            return $this->respondForbidden('Token wrong or expired.');
        }

//        if (!config('boilerplate.reset_password.release_token')) {
        return $this->setStatusCode(204)->respond();
//        }
//
//        $user = $this->user->findByEmail($request->get('email'));
//
//        return $this->respond([
//            'status_code' => 200,
//            'token' => $JWTAuth->fromUser($user)
//        ]);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    protected function broker()
    {
        return Password::broker();
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param  ResetPasswordRequest  $request
     * @return array
     */
    protected function credentials(ResetPasswordRequest $request)
    {
        return $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function reset($user, $password)
    {
        $user->password = $password;
        $user->save();
    }
}
