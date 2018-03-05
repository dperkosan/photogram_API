<?php

namespace App\Api\V1\Controllers;

use Carbon\Carbon;
use Dingo\Api\Http\Request;

class TestController extends ApiController
{
    public function index(Request $request)
    {
        var_dump(isset($request->news_feed));die();
    }
}
