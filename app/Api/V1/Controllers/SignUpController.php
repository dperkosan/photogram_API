<?php
namespace App\Api\V1\Controllers;

use Config;
use App\User;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\SignUpRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Mail\ConfirmationMail;
use Illuminate\Support\Facades\Mail;

class SignUpController extends Controller
{
    public function signUp(SignUpRequest $request, JWTAuth $JWTAuth)
    {
        //create and save user
        $user = new User($request->all());
        if (!$user->save()) {
            throw new HttpException(500);
        }

        //get the token
        $token = $JWTAuth->fromUser($user);

        //send confirmation mail
        $confirmationUrl = url('api/auth/signup/confirmation').'?token='.$token;
        Mail::to($user->email)->send(new ConfirmationMail($confirmationUrl));

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
