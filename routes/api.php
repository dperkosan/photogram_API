<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->group(['version' => 'v1', 'namespace' => 'App\Api\V1\Controllers'], function (Router $api) {
    //authentication
    $api->group(['prefix' => 'auth'], function(Router $api) {
        $api->post('signup', 'SignUpController@signUp');
        $api->post('login', 'LoginController@login');

        $api->post('recovery', 'ForgotPasswordController@sendResetEmail');
        $api->post('reset', 'ResetPasswordController@resetPassword');

        $api->group(['middleware' => 'jwt.auth'], function(Router $api) {
            $api->get('signup/confirmation', 'SignUpController@confirmSignUp')->name('confirmation');
        });
    });
    
    //unprotected posts
    $api->group(['prefix' => 'posts'], function(Router $api) {
        $api->get('list/{numPosts}', 'PostsController@getPosts');
    });

    $api->get('likes', 'LikesController@index');


    $api->group(['middleware' => 'jwt.auth'], function(Router $api) {

        //followers
        $api->get('/followers', 'FollowersController@getFollowers');
        $api->get('/followings', 'FollowersController@getFollowings');
        $api->post('/followers', 'FollowersController@follow');
        $api->delete('/followers/{followed_id}', 'FollowersController@unfollow');

        $api->group(['prefix' => 'users'], function (Router $api) {
            $api->get('/check/{username}', 'UsersController@checkUsername');
            $api->patch('/auth/update', 'UsersController@updateUser');
        });


        $api->get('refresh', [
            'middleware' => 'jwt.refresh',
            function() {
                return response()->json([
                    'message' => 'By accessing this endpoint, you can refresh your access token at each request. Check out this response headers!'
                ]);
            }
        ]);
    });
});
