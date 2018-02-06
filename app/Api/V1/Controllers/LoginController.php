<?php

namespace App\Api\V1\Controllers;

use Tymon\JWTAuth\JWTAuth;
use App\Api\V1\Requests\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LoginController extends ApiController
{
    public function login(LoginRequest $request, JWTAuth $JWTAuth)
    {
        $credentials = $request->only(['password']);
        $requestEmail = $request->email;

        if (filter_var($requestEmail, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $requestEmail;
        } else {
            $credentials['username'] = $requestEmail;
        }

        try {
            $token = $JWTAuth->attempt($credentials);

            if(!$token) {
//                throw new AccessDeniedHttpException();
                return $this->respondForbidden('Invalid credentials');
            }

            //check if user is active (clicked on confirmation mail)
            $currentUser = $JWTAuth->authenticate($token);
            if(!$currentUser->active) {
                return $this->respondForbidden('You need to activate your account first.');
            }

        } catch (JWTException $e) {
            throw new HttpException(500);
        }

        return $this->respond([
            'status_code' => 200,
            'token' => $token,
            'data' => $currentUser
        ]);

    }
}
