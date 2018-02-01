<?php

namespace App\Repositories;

use App\Comment;
use App\Interfaces\CommentRepositoryInterface;
use App\Interfaces\LikeRepositoryInterface;

class CommentRepository extends Repository implements CommentRepositoryInterface
{
    protected $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * @param int $postId
     * @param int $amount
     * @param int $page
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getComments($postId, $amount, $page)
    {
        return $this->fullQuery($amount, $page)
          ->where('post_id', $postId)
          ->get();
    }

    /**
     * @param int $amount
     * @param int $page
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function fullQuery($amount, $page)
    {
        $offset = $this->calcOffset($amount, $page);

        return $this->comment
          ->with('user:id,username,image')
          ->withCount('likes')
          ->orderBy('created_at', 'DESC')
          ->offset($offset)
          ->limit($amount);
    }

    public function addAuthLike($posts, $userId)
    {
        return app(LikeRepositoryInterface::class)->addAuthLikeToComments($posts, $userId);
    }
}