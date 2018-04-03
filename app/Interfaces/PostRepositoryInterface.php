<?php

namespace App\Interfaces;


interface PostRepositoryInterface
{
    public function getPost($id);

    /**
     * @param integer $numPosts
     * @param integer $page
     * @param integer $userId
     * @param string  $hashtag
     *
     * @return mixed
     */
    public function getPosts($numPosts, $page, $userId, $hashtag);
    public function newsFeed($userId, $amount, $page);

    public function addComments($posts, $limit = 5);
    public function addCommentsToSingePost($post, $limit = 5);
//    public function addThumbs($posts);
    public function addAuthLike($posts, $userId);

    
}