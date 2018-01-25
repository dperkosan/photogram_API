<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\UserRequest;
use App\User;
use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\Input;

class TestController extends ApiController
{
    public function index(Request $request)
    {
        $requestData = $request->only([
          'username', 'email', 'name', 'gender_id', 'phone', 'about'
        ]);
        $validator = \Validator::make(
            $request->only(['username']),
            [
              'username' => 'max:4'
            ]
        );


        dd($validator->passes());

        return $requestData;
    }
}
