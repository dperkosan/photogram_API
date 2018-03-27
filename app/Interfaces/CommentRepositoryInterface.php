<?php

namespace App\Interfaces;


interface CommentRepositoryInterface
{
    public function getComments($postId, $amount, $page);
    public function addAuthLike($posts, $userId);
    public function create($commentData);
    public function save($comment, array $commentData);

    public function getComment($id);
}