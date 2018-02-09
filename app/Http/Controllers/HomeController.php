<?php

namespace App\Http\Controllers;

use App\Api\V1\Documentation\Generator;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class HomeController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        return view('welcome');
    }
    
    public function documentation()
    {
        $endpoints = Generator::getInstance()->getData();

        return view('documentation', compact('endpoints'));
    }
}