<?php

namespace App\Interfaces;


interface PostRepositoryInterface
{
    /**
     * Get all posts
     *
     * @param integer $numPosts
     * @param integer $page
     *
     * @return mixed
     */
    public function getPosts($numPosts, $page);

    
}