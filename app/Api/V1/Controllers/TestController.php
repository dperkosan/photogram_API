<?php

namespace App\Api\V1\Controllers;

use Dingo\Api\Http\Request;

class TestController extends ApiController
{
    public function index(Request $request)
    {
        $array = [
          [
            1 => 'a',
            2 => 'b',
          ],
          [
            1 => 'c',
            2 => 'd',
          ],
        ];

        $element = [
          1 => 'a',
          2 => 'd',
        ];

        return $this->respondWithData(in_array($element, $array));
    }
}
