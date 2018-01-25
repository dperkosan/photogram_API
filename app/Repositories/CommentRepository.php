<?php

namespace App\Repositories;

use App\Interfaces\CommentRepositoryInterface;
use App\Post;

class CommentRepository implements CommentRepositoryInterface
{
//    /*
//     * @var \App\Comment
//     */
//    private $comment;
//
//    public function __construct($comment)
//    {
//        $this->comment = $comment;
//    }

    public function getComments($postId)
    {
        return Post::find($postId)->comments();
    }
}