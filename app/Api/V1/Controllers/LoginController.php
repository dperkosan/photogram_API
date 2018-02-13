<?php

namespace App\Api\V1\Controllers;

use App\Interfaces\UserRepositoryInterface;
use Tymon\JWTAuth\JWTAuth;
use App\Api\V1\Requests\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LoginController extends ApiController
{
    public function login(LoginRequest $request, JWTAuth $JWTAuth, UserRepositoryInterface $userRepository)
    {
        $credentials = $request->only(['password']);
        $emailOrUsername = $request->email;

        if (filter_var($emailOrUsername, FILTER_VALIDATE_EMAIL)) {
            if (!$userRepository->emailExists($emailOrUsername)) {
                return $this->respondForbidden('This email is not registered.');
            }

            $credentials['email'] = $emailOrUsername;
        } else {
            if (!$userRepository->usernameExists($emailOrUsername)) {
                return $this->respondForbidden('This username is not registered.');
            }

            $credentials['username'] = $emailOrUsername;
        }

        try {
            $token = $JWTAuth->attempt($credentials);

            if(!$token) {
//                throw new AccessDeniedHttpException();
                return $this->respondForbidden('Wrong password.');
            }

            //check if user is active (clicked on confirmation mail)
            $currentUser = $JWTAuth->authenticate($token);
            if(!$currentUser->active) {
                return $this->respondForbidden('You need to activate your account first.');
            }

        } catch (JWTException $e) {
            throw new HttpException(500);
        }

        $userRepository->addCounts($currentUser);
        $userRepository->addThumbs($currentUser);

        return $this->respond([
            'status_code' => 200,
            'token' => $token,
            'data' => $currentUser
        ]);

    }
}
