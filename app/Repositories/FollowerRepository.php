<?php

namespace App\Repositories;


use App\User;
use App\Follower;
use App\Interfaces\FollowerRepositoryInterface;

class FollowerRepository extends Repository implements FollowerRepositoryInterface
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var Follower
     */
    private $follower;

    public function __construct(User $user, Follower $follower)
    {
        $this->user = $user;
        $this->follower = $follower;
    }

    /**
     * Get followers for authenticated user
     *
     * @param $amount
     * @param $page
     * @param $followedId
     *
     * @return mixed
     */
    public function getFollowers($amount, $page, $followedId)
    {
        $offset = $this->calcOffset($amount, $page);

        return $this->user->find($followedId)->followers()->offset($offset)->limit($amount)->get();
    }

    /**
     * Get followings for authenticated user
     *
     * @param $followerId
     *
     * @return mixed
     */
    public function getFollowings($followerId)
    {
        return $this->follower->where('follower_id', $followerId)->get();
    }

    /**
     * Follow another user
     *
     * @param $followerId
     * @param $followedId
     * @return bool|Follower
     */
    public function follow($followerId, $followedId)
    {
        $follow = new Follower();
        $follow->follower_id = $followerId;
        $follow->followed_id = $followedId;
        if ($follow->save()) {
            return $follow;
        }

        return false;
    }

    /**
     * Check if following already exists
     *
     * @param $followerId
     * @param $followedId
     * @return bool
     */
    public function followExists($followerId, $followedId)
    {
        return ($this->follower->where(['follower_id' => $followerId, 'followed_id' => $followedId])->count() > 0);
    }

    /**
     * Check if followed user exists
     *
     * @param $user_id
     * @return bool
     */
    public function userExists($user_id)
    {
        return $this->user->find($user_id)->exists();
    }

    /**
     * Unfollow another user
     *
     * @param $followerId
     * @param $followedId
     * @return bool
     */
    public function unfollow($followerId, $followedId)
    {
        $follow = $this->follower->where(['follower_id' => $followerId, 'followed_id' => $followedId])->first();
        if ($follow->delete()) {
            return true;
        }

        return false;
    }
}