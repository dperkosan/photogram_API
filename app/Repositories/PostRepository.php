<?php

namespace App\Repositories;

use App\Interfaces\FollowerRepositoryInterface;
use App\Interfaces\LikeRepositoryInterface;
use App\Post;
use App\Interfaces\PostRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PostRepository extends Repository implements PostRepositoryInterface
{
    /**
     * @var \App\Post
     */
    private $post;
    private $follower;

    const TYPE_IMAGE = 1;
    const TYPE_VIDEO = 2;

    public function __construct(Post $post, FollowerRepositoryInterface $follower)
    {
        $this->post = $post;
        $this->follower = $follower;
    }

    /**
     * Get latest posts
     *
     * @param integer $amount number of posts to return
     * @param int     $page
     * @param null    $userId
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPosts($amount, $page = 1, $userId = null)
    {
        $query = $this->fullQuery($amount, $page);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->get();
    }

    /**
     * This is for the home page, so it returns posts from followed users
     *
     * @param int $userId
     * @param int $amount
     * @param int $page
     *
     * @return Collection
     */
    public function newsFeed($userId, $amount, $page = 1)
    {
        $followedIds = $this->follower->getFollowings($userId)->pluck('followed_id');

        return $this->fullQuery($amount, $page)
          ->whereIn('user_id', $followedIds)
          ->get();
    }

    /**
     * Post with user, comments*, comments_count, likes_count, order, offset, limit
     *
     * comments* with user, likes_count, limit 5
     *
     * @param int $amount
     * @param int $page
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function fullQuery($amount = 3, $page)
    {
        $offset = $this->calcOffset($amount, $page);

        return $this->post
          ->with('user:id,username,image')
          ->with(['comments' => function($query){
              return $query
                ->with('user:id,username,image')
                ->withCount('likes')
                ->take(5);
          }])
          ->withCount('comments')
          ->withCount('likes')
          ->orderBy('created_at', 'DESC')
          ->offset($offset)
          ->limit($amount);
    }

    /**
     * Adds is_liked (by auth user) to every post and nested comment
     *
     * @param Collection $posts
     * @param integer    $userId
     *
     * @return Collection
     */
    public function addAuthLike($posts, $userId)
    {
        return app(LikeRepositoryInterface::class)->addAuthLikeToPosts($posts, $userId);
    }
}