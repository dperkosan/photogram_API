<?php

namespace App\Api\V1\Controllers;

use Auth;
use Tymon\JWTAuth\JWTAuth;
use App\Interfaces\PostRepositoryInterface;
use App\Validators\Post\CreatePostValidator;

class PostsController extends ApiController
{
    /**
     * @var \App\Interfaces\FollowerRepositoryInterface
     */
    private $posts;

    /**
     * @var CreatePostValidator
     */
    private $createPostValidator;

    public function __construct(JWTAuth $jwtAuth, PostRepositoryInterface $posts, CreatePostValidator $createPostValidator)
    {
        $this->jwtAuth = $jwtAuth;
        $this->posts = $posts;
        $this->createPostValidator = $createPostValidator;
    }

    /**
     * Get x number of latest posts
     *
     * @param $numPosts
     * @return mixed
     */
    public function getPosts($numPosts)
    {
//        $dbgBAarHead = \Debugbar::getJavascriptRenderer()->renderHead();
//        $dbgBar = \Debugbar::getJavascriptRenderer()->render();
//        echo $dbgBAarHead . $dbgBar;
        return $this->posts->getPosts($numPosts);
    }
}