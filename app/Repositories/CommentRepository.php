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
     * @param int $commentId
     * @param int $amount
     * @param int $page
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getComments($postId, $commentId = null, $amount, $page)
    {
        $query = $this->fullQuery($amount, $page)
          ->where('post_id', $postId);

        if ($commentId) {
            $query->where('comment_id', $commentId);
        }

        return $query->get();
    }

    /**
     * @param int $amount
     * @param int $page
     *
     * @return \Illuminate\Database\Eloquent\Builder|mixed
     */
    protected function fullQuery($amount, $page)
    {
        $offset = $this->calcOffset($amount, $page);

        return $this->comment
          ->select(['comments.*', 'users.username', 'users.image as user_image'])
          ->join('users', 'users.id', '=' , 'comments.user_id')
          ->withCount('likes')
          ->orderBy('created_at', 'DESC')
          ->offset($offset)
          ->limit($amount);
    }

    public function addAuthLike($comments, $userId)
    {
        return app(LikeRepositoryInterface::class)->addAuthLikeToComments($comments, $userId);
    }
}