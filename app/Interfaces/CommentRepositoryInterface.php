<?php

namespace App\Interfaces;


interface CommentRepositoryInterface
{
    public function getComments($postId, $amount, $page);

    public function addAuthLike($posts, $userId);
}