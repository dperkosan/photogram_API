<?php

use Illuminate\Routing\Router;

/** @var Router $router */
$router = app(Router::class);

$router->group(['namespace' => 'App\Http\Controllers'], function (Router $router) {

    $router->get('/', 'Controller@index');

    $router->get('/documentation', 'Controller@documentation');

});