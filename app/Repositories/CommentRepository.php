<?php

namespace App\Repositories;

use App\Comment;
use App\HashtagsLink;
use App\Interfaces\CommentRepositoryInterface;
use App\Interfaces\HashtagRepositoryInterface;
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
        $this->processCommentData($commentData);

        $comment = $this->comment->create($commentData);

        app(HashtagRepositoryInterface::class)->saveHashtags($comment->id, HashtagsLink::TAGGABLE_COMMENT, $comment->body);

        return $comment;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $comment
     * @param array                               $commentData
     *
     * @return mixed
     */
    public function save($comment, array $commentData)
    {
        $this->processCommentData($commentData);

        if (isset($commentData['body'])) {
            $comment->body = $commentData['body'];
            app(HashtagRepositoryInterface::class)->saveHashtags($comment->id, HashtagsLink::TAGGABLE_COMMENT, $comment->body);
        }

        if (isset($commentData['reply_user_id'])) {
            $comment->reply_user_id = $commentData['reply_user_id'];
        }

        return $comment->save();
    }

    protected function processCommentData(&$commentData)
    {
        // Transform reply_username into reply_user_id and/or remove reply_username
        if (!empty($commentData['reply_username'])) {
            if (empty($commentData['reply_user_id'])) {
                $commentData['reply_user_id'] = app(UserRepositoryInterface::class)->getUserIdFromUsername($commentData['reply_username']);
            }
            unset($commentData['reply_username']);
        }
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
        $query = $this->fullQuery($amount, $page)
            ->where('post_id', $postId);

        return $query->get();
    }

    public function getComment($id)
    {
        return $this->fullQueryById($id)->first();
    }

    /**
     * @param int $amount
     * @param int $page
     *
     * @return \Illuminate\Database\Eloquent\Builder|mixed
     */
    public function fullQuery($amount = 1, $page = 1)
    {
        $offset = $this->calcOffset($amount, $page);

        return $this->baseFullQuery()
            ->orderBy('created_at', 'DESC')
            ->offset($offset)
            ->limit($amount);
    }

    protected function fullQueryById($id)
    {
        return $this->baseFullQuery()->where('comments.id', '=', $id);
    }

    protected function baseFullQuery()
    {
        return $this->comment
            ->select([
                'comments.*',
                'users.username',
                'users.image as user_image',
                'reply_users.id as reply_user_id',
                'reply_users.username as reply_username',
            ])
            ->join('users', 'users.id', '=', 'comments.user_id')
            ->leftJoin('users as reply_users', 'reply_users.id', '=', 'comments.reply_user_id')
            ->withCount('likes');
    }

    public function addAuthLike($comments, $userId)
    {
        return app(LikeRepositoryInterface::class)->addAuthLikeToComments($comments, $userId);
    }
}