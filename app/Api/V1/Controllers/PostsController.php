<?php

namespace App\Api\V1\Controllers;

use App\Interfaces\PostRepositoryInterface;
use App\Post;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

class PostsController extends ApiController
{
    /**
     * @var \App\Interfaces\FollowerRepositoryInterface
     */
    private $posts;
    private $jwtAuth;

    public function __construct(JWTAuth $jwtAuth, PostRepositoryInterface $posts)
    {
        $this->jwtAuth = $jwtAuth;
        $this->posts = $posts;
    }

    /**
     * Get x number of latest posts
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function getPosts(Request $request)
    {
        if (!$request->amount || !$request->page) {
            return $this->respondWrongArgs('Query parameters \'amount\' and \'page\' are required.');
        }

        return $this->posts->getPosts($request->amount, $request->page)->toJson();
    }
    
    public function likes($id)
    {
        $likes = $this->posts->getLikes($id);
    }
}