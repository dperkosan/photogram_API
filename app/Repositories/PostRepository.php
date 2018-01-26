<?php

namespace App\Repositories;

use App\Post;
use App\Interfaces\PostRepositoryInterface;

class PostRepository implements PostRepositoryInterface
{
    /**
     * @var \App\Post
     */
    private $post;

    const TYPE_IMAGE = 1;
    const TYPE_VIDEO = 2;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Get latest posts
     *
     * @param integer $numPosts number of posts to return
     * @param int     $page
     *
     * @return Post
     */
    public function getPosts($numPosts, $page = 1)
    {
        $offset = ($page - 1) * $numPosts;

        return $this->post
          ->orderBy('created_at', 'DESC')
          ->with('comments')
          ->offset($offset)
          ->limit($numPosts)
          ->get();
    }

    public function getLikes($postId)
    {
        $this->post->find($postId)->likes;
    }


}