<?php
namespace App\Api\V1\Controllers;

use Tymon\JWTAuth\JWTAuth;
use App\Api\V1\Requests\SignUpRequest;
use App\Interfaces\UserRepositoryInterface;

class SignUpController extends ApiController
{
    /**
     * @var UserRepositoryInterface
     */
    private $user;


    public function __construct(UserRepositoryInterface $user)
    {
        $this->user = $user;
    }

    public function signUp(SignUpRequest $request, JWTAuth $JWTAuth)
    {
        $user = $this->user->store($request->all());

        if(!$user) {
            return $this->respondInternalError();
        }

        //get the token
        $token = $JWTAuth->fromUser($user);
        //send confirmation mail
        $this->user->sendConfirmEmailNotification($token);

        //send response with or without token
        if (!config('boilerplate.sign_up.release_token')) {
            return $this->respondCreated();
        }

        return $this->setStatusCode(201)->respond(['token' => $token]);
    }

    public function confirmSignUp(JWTAuth $JWTAuth)
    {
        $currentUser = $JWTAuth->parseToken()->authenticate();
        $currentUser->active = true;
        if (!$currentUser->save()) {
            return $this->respondInternalError();
        }
        return "Thank you for your confirmation!";
    }
}
