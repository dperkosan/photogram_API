<?php

namespace App\Api\V1\Controllers;

use Dingo\Api\Http\Request;

class TestController extends ApiController
{
    public function index(Request $request)
    {
        $data = [];

        $data = ceil(-19/20);

        return $this->respondWithData($data);
    }
}
