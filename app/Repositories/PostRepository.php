<?php

namespace App\Repositories;

use App\Post;
use App\Interfaces\PostRepositoryInterface;

class PostRepository implements PostRepositoryInterface
{
    /**
     * @var post
     */
    private $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Get latest posts
     *
     * @param integer $numPosts number of posts to return
     * @return \App\Post
     */
    public function getPosts($numPosts)
    {
        return $this->post->orderBy('created_at', 'DESC')->limit($numPosts)->get();
    }
}