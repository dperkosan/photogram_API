<?php
namespace App\Api\V1\Controllers;

use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\JWTAuth;
use App\Api\V1\Requests\SignUpRequest;
use App\Validators\User\CreateUserValidator;
use App\Interfaces\UserRepositoryInterface;

class SignUpController extends ApiController
{
    /**
     * @var UserRepositoryInterface
     */
    private $user;

    /**
     * @var CreateUserValidator
     */
    private $createUserValidator;

    public function __construct(UserRepositoryInterface $user, CreateUserValidator $createUserValidator)
    {
        $this->user = $user;
        $this->createUserValidator = $createUserValidator;
    }

    public function signUp(SignUpRequest $request, JWTAuth $JWTAuth)
    {
//        //validate input
//        if(!$this->createUserValidator->passes())
//        {
//            return $this->respondWrongArgs($this->createUserValidator->errors);
//        }

        //create and save user
        $user = $this->user->store($request->all());

        if(!$user) {
            return $this->respondInternalError();
        }

        //get the token
        $token = $JWTAuth->fromUser($user);
        //send confirmation mail
        $this->user->sendConfirmEmailNotification($token);

        //send response with or without token
        if (!Config::get('boilerplate.sign_up.release_token')) {
            return $this->setStatusCode(201)->respond(['message' => 'success']);
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
