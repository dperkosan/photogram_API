<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    //authentication
    $api->group(['prefix' => 'auth'], function(Router $api) {
        $api->post('signup', 'App\\Api\\V1\\Controllers\\SignUpController@signUp');
        $api->post('login', 'App\\Api\\V1\\Controllers\\LoginController@login');

        $api->post('recovery', 'App\\Api\\V1\\Controllers\\ForgotPasswordController@sendResetEmail');
        $api->post('reset', 'App\\Api\\V1\\Controllers\\ResetPasswordController@resetPassword');
    });
    
    $api->group(['prefix' => 'auth', 'middleware' => 'jwt.auth'], function(Router $api) {
        $api->get('signup/confirmation', 'App\\Api\\V1\\Controllers\\SignUpController@confirmSignUp')->name('confirmation');
    });

    $api->group(['middleware' => 'jwt.auth'], function(Router $api) {
        $api->get('protected', function() {
            return response()->json([
                'message' => 'Access to this item is only for authenticated user. Provide a token in your request!'
            ]);
        });

        //followers
        $api->get('/followers', 'App\\Api\\V1\\Controllers\\FollowersController@getFollowers');
        $api->post('/followers', 'App\\Api\\V1\\Controllers\\FollowersController@follow');
        $api->delete('/followers/{followed_id}', 'App\\Api\\V1\\Controllers\\FollowersController@unfollow');

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
