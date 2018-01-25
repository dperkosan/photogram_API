<?php

namespace App\Api\V1\Controllers;

use App\Events\NewFollower;
use App\Repositories\PostRepository;
use App\User;
use Tymon\JWTAuth\JWTAuth;
use App\Validators\Post\CreatePostValidator;

class PostsController extends ApiController
{
    /**
     * @var \App\Interfaces\FollowerRepositoryInterface
     */
    private $posts;
    private $jwtAuth;

    public function __construct(JWTAuth $jwtAuth, PostRepository $posts)
    {
        $this->jwtAuth = $jwtAuth;
        $this->posts = $posts;
    }

    /**
     * Get x number of latest posts
     *
     * @param $numPosts
     * @return mixed
     */
    public function getPosts($numPosts)
    {
        return $this->posts->getPosts($numPosts)->toJson();
    }
    
    public function likes($id)
    {
        $likes = $this->posts->getLikes($id);
    }
}