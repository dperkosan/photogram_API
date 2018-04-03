<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->group(['version' => 'v1', 'namespace' => 'App\Api\V1\Controllers'], function (Router $api) {

    $api->group(['prefix' => 'auth'], function(Router $api) {
        $api->post('signup', 'SignUpController@signUp');
        $api->post('login', 'LoginController@login');

        $api->post('recovery', 'ForgotPasswordController@sendResetEmail');
        $api->post('reset', 'ResetPasswordController@resetPassword')->name('post.reset.password');

        $api->group(['middleware' => 'jwt.auth'], function(Router $api) {
            $api->get('signup/confirmation', 'SignUpController@confirmSignUp')->name('confirmation');
        });
    });

    $api->get('/test', 'TestController@index');

    $api->get('/config', 'ConfigController@index');
    
    // unprotected posts JUST FOR TESTING
    $api->group(['prefix' => 'posts'], function(Router $api) {
        $api->get('/test', 'PostsController@index');
    });

    $api->get('/users/exists', 'UsersController@exists');

    $api->get('/elastic/add-to-index/posts', 'ElasticController@testIndexPosts');
    $api->get('/elastic/add-to-index/users', 'ElasticController@testIndexUsers');
    $api->get('/elastic/add-to-index/hashtags', 'ElasticController@testIndexHashtags');
    $api->get('/elastic/search', 'ElasticController@search');

    $api->group(['middleware' => 'jwt.auth'], function(Router $api) {

        $api->group(['prefix' => 'followers'], function (Router $api) {
            $api->get('/', 'FollowersController@getFollowers');
            $api->get('/mutual', 'FollowersController@getMutual');
            $api->post('/', 'FollowersController@follow');
            $api->delete('/{user_id}', 'FollowersController@unfollow');
        });

        $api->get('/followings', 'FollowersController@getFollowings');

        $api->group(['prefix' => 'users'], function (Router $api) {

            $api->get('/find', 'UsersController@find');
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
            $api->patch('{post}', 'PostsController@update');
            $api->delete('{post}', 'PostsController@destroy');
        });

        $api->group(['prefix' => 'comments'], function(Router $api) {
            $api->get('/', 'CommentsController@index');
            $api->post('/', 'CommentsController@store');
            $api->patch('{comment}', 'CommentsController@update');
            $api->delete('{comment}', 'CommentsController@destroy');
        });

        $api->group(['prefix' => 'likes'], function(Router $api) {
            $api->get('/', 'LikesController@index');
            $api->post('/', 'LikesController@store');
            $api->delete('{like}', 'LikesController@destroy');
        });

        $api->group(['prefix' => 'search'], function (Router $api) {
            $api->get('/users', 'SearchController@searchUsername');
            $api->get('/hashtags', 'SearchController@searchHashtag');
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
