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
     * @return \App\Post
     */
    public function getPosts($numPosts)
    {
        return $posts = $this->post->orderBy('created_at', 'DESC')->with(['user:id,username,image', 'comments'])->limit($numPosts)->get();

        $processedPosts = [];

        foreach ($posts as $post) {
            foreach ($post->comments as $comment) {

            }
        }
    }
}