<?php
namespace App\Api\V1\Controllers;

use Config;
use Tymon\JWTAuth\JWTAuth;
use App\Api\V1\Requests\SignUpRequest;
use App\Validators\CreateUserValidator;
use App\Api\V1\Controllers\ApiController;
use App\Interfaces\UserRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        //validate input
        if(!$this->createUserValidator->passes())
        {
            return $this->respondWrongArgs($this->createUserValidator->errors);
        }

        //create and save user
        $user = $this->user->store($request->all());
        if(!$user)
        {
            return $this->respondInternalError();
        }

        //get the token
        $token = $JWTAuth->fromUser($user);

        //send confirmation mail
        $user->sendConfirmEmailNotification($token);

        //send response with or without token
        if (!Config::get('boilerplate.sign_up.release_token')) {
            return response()->json([
                    'status' => 'ok'
                    ], 201);
        }

        return response()->json([
                'status' => 'ok',
                'token' => $token
                ], 201);
    }

    public function confirmSignUp(JWTAuth $JWTAuth)
    {
        $currentUser = $JWTAuth->parseToken()->authenticate();
        $currentUser->active = true;
        if (!$currentUser->save()) {
            throw new HttpException(500);
        }
        return "Thank you for your confirmation!";
    }
}
