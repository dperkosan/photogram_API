<?php

namespace App\Repositories;

use App\Interfaces\LikeRepositoryInterface;
use App\Like;
use Illuminate\Support\Collection;

class LikeRepository extends Repository implements LikeRepositoryInterface
{
    protected $like;
    protected $likablePost;
    protected $likableComment;

    public function __construct(Like $like)
    {
        $this->like = $like;
        $this->likablePost = $like::LIKABLE_POST;
        $this->likableComment = $like::LIKABLE_COMMENT;
    }

    /**
     * Adds auth_like_id (by auth user) to every post and nested comment
     *
     * @param Collection $posts
     * @param integer    $authUserId user id of the auth user
     *
     * @return Collection| $posts
     */
    public function addAuthLikeToPosts($posts, $authUserId)
    {
        if (!($posts instanceof Collection)) {
            return $this->addAuthLikeToSinglePost($posts, $authUserId);
        }

        $likableIds = [];

        foreach ($posts as $post) {
            $likableIds[] = $post->id;
            foreach ($post->comments as $comment) {
                $likableIds[] = $comment->id;
            }
        }

        $likes = $this->getLikesForAuth(array_unique($likableIds), $authUserId);

        foreach ($posts as $post) {
            $this->addLikeId($post, $this->likablePost, $likes);

            foreach ($post->comments as $comment) {
                $this->addLikeId($comment, $this->likableComment, $likes);
            }
        }

        return $posts;
    }

    protected function addAuthLikeToSinglePost($post, $authUserId)
    {
        $likableIds = [];

        $likableIds[] = $post->id;
        foreach ($post->comments as $comment) {
            $likableIds[] = $comment->id;
        }

        $likes = $this->getLikesForAuth(array_unique($likableIds), $authUserId);

        $this->addLikeId($post, $this->likablePost, $likes);

        foreach ($post->comments as $comment) {
            $this->addLikeId($comment, $this->likableComment, $likes);
        }

        return $post;
    }

    /**
     * Adds auth_like_id (by auth user) to every comment
     *
     * @param Collection $comments
     * @param integer    $userId user id of the auth user
     *
     * @return Collection $posts
     */
    public function addAuthLikeToComments($comments, $userId)
    {
        $likableIds = [];

        foreach ($comments as $comment) {
            $likableIds[] = $comment->id;
        }

        $likes = $this->getLikesForAuth($likableIds, $userId);

        foreach ($comments as $comment) {
            $this->addLikeId($comment, $this->likableComment, $likes);
        }

        return $comments;
    }

    /**
     * Used when finding out if auth user liked a likable model
     *
     * @param array $likableIds
     * @param int   $userId user id of the auth user
     *
     * @return Collection
     */
    protected function getLikesForAuth($likableIds, $userId)
    {
        return $this->like
          ->whereIn('likable_id', $likableIds)
          ->where('user_id', $userId)
          ->get();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param int                                 $likableType
     * @param Collection                          $likes
     */
    protected function addLikeId($model, $likableType, $likes)
    {
        $like = $likes
          ->where('likable_id', $model->id)
          ->where('likable_type', $likableType);

        $model->auth_like_id = $like->isNotEmpty() ? $like->first()->id : null;
    }
}