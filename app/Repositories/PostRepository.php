<?php

namespace App\Repositories;

use App\Post;
use App\Interfaces\PostRepositoryInterface;

class PostRepository implements PostRepositoryInterface
{
    /**
     * @var Post
     */
    private $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Get followers for authenticated user
     *
     * @param $numPosts
     * @return mixed
     */
    public function getPosts($numPosts)
    {
        return $this->post->orderBy('created_at', 'DESC')->limit($numPosts)->get();
    }
}