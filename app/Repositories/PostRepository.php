<?php

namespace App\Repositories;

use App\Follower;
use App\Interfaces\FollowerRepositoryInterface;
use App\Post;
use App\Interfaces\PostRepositoryInterface;
use Illuminate\Database\Query\Builder;

class PostRepository implements PostRepositoryInterface
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

    public function newsFeed($userId, $amount, $page = 1)
    {
        $followedIds = $this->follower->getFollowings($userId)->pluck('followed_id');

        return $this->fullQuery($amount, $page)
          ->whereIn('user_id', $followedIds)
          ->get();
    }

    private function calcOffset($amount, $page)
    {
        return ($page - 1) * $amount;
    }

    private function fullQuery($amount = 3, $page)
    {
        $offset = $this->calcOffset($amount, $page);

        return $this->post->with('user:id,username,image')
          ->with(['comments' => function($query){
              return $query
                ->with('user:id,username,image')
                ->withCount('likes')
                ->take(5);
          }])
          ->with('user:id,username,image')
          ->withCount('comments')
          ->withCount('likes')
          ->orderBy('created_at', 'DESC')
          ->offset($offset)
          ->limit($amount);
    }
}