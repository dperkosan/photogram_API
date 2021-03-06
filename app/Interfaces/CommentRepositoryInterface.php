<?php

namespace App\Interfaces;


interface CommentRepositoryInterface
{
    public function getComments($postId, $commentId, $amount, $page);

    public function addAuthLike($posts, $userId);
}