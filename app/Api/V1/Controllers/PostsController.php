<?php

namespace App\Api\V1\Controllers;

use App\Interfaces\PostRepositoryInterface;
use App\Post;
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