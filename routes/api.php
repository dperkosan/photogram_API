<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->group(['version' => 'v1', 'namespace' => 'App\Api\V1\Controllers'], function (Router $api) {

    $api->group(['prefix' => 'auth'], function(Router $api) {
        $api->post('signup', 'SignUpController@signUp');
        $api->post('login', 'LoginController@login');

        $api->post('recovery', 'ForgotPasswordController@sendResetEmail');
        $api->post('reset', 'ResetPasswordController@resetPassword');

        $api->group(['middleware' => 'jwt.auth'], function(Router $api) {
            $api->get('signup/confirmation', 'SignUpController@confirmSignUp')->name('confirmation');
        });
    });

    $api->get('/test', 'TestController@index');

    $api->get('/config', 'ConfigController@index');
    
    // unprotected posts JUST FOR TESTING
    $api->group(['prefix' => 'posts'], function(Router $api) {
        $api->get('list', 'PostsController@getPosts');
    });

    $api->group(['middleware' => 'jwt.auth'], function(Router $api) {

        $api->get('/followers', 'FollowersController@getFollowers');
        $api->get('/followings', 'FollowersController@getFollowings');
        $api->post('/followers', 'FollowersController@follow');
        $api->delete('/followers/{followed_id}', 'FollowersController@unfollow');

        $api->group(['prefix' => 'users'], function (Router $api) {
            $api->get('/check/{username}', 'UsersController@checkUsername');

            $api->group(['prefix' => 'auth'], function (Router $api) {
                $api->get('/', 'UsersController@getAuthUser');
                $api->post('/image', 'UsersController@updateAuthProfileImage');
                $api->patch('/update', 'UsersController@updateAuthUser');
            });
        });

        $api->get('/home', 'PostsController@newsFeed');

        $api->group(['prefix' => 'posts'], function(Router $api) {
            $api->get('/', 'PostsController@index');
            $api->get('{post}', 'PostsController@show');
            $api->post('/', 'PostsController@store');
            $api->patch('/{post}', 'PostsController@update');
            $api->delete('/{post}', 'PostsController@destroy');
        });

        $api->group(['prefix' => 'likes'], function(Router $api) {
            $api->get('/', 'LikesController@index');
            $api->post('/', 'LikesController@store');
            $api->delete('/', 'LikesController@destroy');
        });

        $api->get('refresh', ['middleware' => 'jwt.refresh', function() {
                return response()->json([
                  'success' => true,
                  'message' => 'Token is refreshed, grab it from the header.'
                ]);
            }
        ]);
    });
});
