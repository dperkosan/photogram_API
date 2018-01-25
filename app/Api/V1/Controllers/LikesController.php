<?php

namespace App\Api\V1\Controllers;

class LikesController extends ApiController
{
    public function index()
    {
        return request()->all();
    }
}