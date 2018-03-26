<?php

namespace App\Repositories;

use App\Comment;
use App\Interfaces\CommentRepositoryInterface;
use App\Interfaces\LikeRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;

class CommentRepository extends Repository implements CommentRepositoryInterface
{
    protected $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * @param array $commentData
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create($commentData)
    {
        // Transform reply_username into reply_user_id and/or remove reply_username
        if (!empty($commentData['reply_username'])) {
            if (!empty($commentData['reply_user_id'])) {
                unset($commentData['reply_username']);
            } else {
                $commentData['reply_user_id'] = app(UserRepositoryInterface::class)->getUserIdFromUsername($commentData['reply_username']);
            }
        }

        return $this->comment->create($commentData);
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
            ->select([
                'comments.*',
                'users.username',
                'users.image as user_image',
                'reply_users.id as reply_user_id',
                'reply_users.username as reply_username',
            ])
            ->join('users', 'users.id', '=', 'comments.user_id')
            ->join('users as reply_users', 'users.id', '=', 'comments.reply_user_id')
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