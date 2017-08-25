<?php

namespace App\Interfaces;


interface PostRepositoryInterface
{
    /**
     * Get all posts
     *
     * @param $numPosts
     * @return mixed
     */
    public function getPosts($numPosts);

    
}