<?php

use Illuminate\Routing\Router;

/** @var Router $router */
$router = app(Router::class);

//$router->group(['namespace' => '\App\Http\Controllers'], function (Router $router) {

    $router->get('/', '\App\Http\Controllers\HomeController@index');

    $router->get('/documentation', '\App\Http\Controllers\HomeController@documentation');

//});