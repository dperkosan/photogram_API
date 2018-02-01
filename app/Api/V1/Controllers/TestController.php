<?php

namespace App\Api\V1\Controllers;

use Dingo\Api\Http\Request;

class TestController extends ApiController
{
    public function index(Request $request)
    {
        return $this->respondWithData($this->authUser());
    }
}
