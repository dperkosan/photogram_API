<?php

namespace App\Api\V1\Controllers;

use Carbon\Carbon;
use Dingo\Api\Http\Request;

class TestController extends ApiController
{
    public function index(Request $request)
    {
        $period = new \DatePeriod(
          new \DateTime(),
          new \DateInterval('PT1S'),
          10
        );

        $a = [];
        foreach ($period as $date) {
            $a[] = $date->format('Y-m-d H:i:s');
        }

        return $this->respondWithData($a);
    }
}
