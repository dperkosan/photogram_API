<?php

namespace App\Repositories;

use App\Interfaces\LikeRepositoryInterface;
use App\Like;
use Illuminate\Database\Eloquent\Collection;

class LikeRepository extends Repository implements LikeRepositoryInterface
{
    protected $like;
    protected $likablePost;
    protected $likableComment;

    public function __construct(Like $like)
    {
        $this->like = $like;
        $this->likablePost = $like::LIKABLE_POST;
        $this->likableComment = $like::LIKABLE_POST;
    }

    public function usersFromLikes($likableId, $likableType, $amount, $page)
    {
        $offset = $this->calcOffset($amount, $page);

        return $this->like
          ->select(['users.id', 'users.username', 'users.image'])
          ->join('users', 'users.id', '=', 'likes.user_id')
          ->where([
            'likable_id' => $likableId,
            'likable_type' => $likableType,
          ])
          ->offset($offset)
          ->limit($amount)
          ->get();
    }

    /**
     * Adds auth_like_id (by auth user) to every post and nested comment
     *
     * @param Collection $posts
     * @param integer    $userId user id of the auth user
     *
     * @return Collection $posts
     */
    public function addAuthLikeToPosts($posts, $userId)
    {
        $likableIds = [];

        foreach ($posts as $post) {
            $likableIds[] = $post->id;
            foreach ($post->comments as $comment) {
                $likableIds[] = $comment->id;
            }
        }

        $likes = $this->getLikesForAuth(array_unique($likableIds), $userId);

        if ($likes->isNotEmpty()) {

            foreach ($posts as $post) {
                $this->addLikeId($post, $this->likablePost, $likes);

                foreach ($post->comments as $comment) {
                    $this->addLikeId($comment, $this->likableComment, $likes);
                }
            }
        }

        return $posts;
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

        if ($likes->isNotEmpty()) {
            foreach ($comments as $comment) {
                $this->addLikeId($comment, $this->likableComment, $likes);
            }
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