<?php

namespace App\Repositories;


use App\User;
use App\Follower;
use App\Interfaces\FollowerRepositoryInterface;

class FollowerRepository implements FollowerRepositoryInterface
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
     * @param $followedId
     * @return mixed
     */
    public function getFollowers($followedId)
    {
        $user = $this->user->find($followedId);
        return $user->followers()->get();
    }

    /**
     * Get followings for authenticated user
     *
     * @param $followerId
     * @return mixed
     */
    public function getFollowings($followerId)
    {
        $user = $this->user->find($followerId);
        return $user->followings()->get();
    }

    /**
     * Follow another user
     *
     * @param $followerId
     * @param $followedId
     * @return bool
     */
    public function follow($followerId, $followedId)
    {
        $follow = $this->fillFollowerObject($this->follower, $followerId, $followedId);
        if($follow->save()) return $follow;

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
        $user = $this->user->find($user_id);
        return $user ? true : false;
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
        if($follow->delete()) return true;

        return false;
    }

    /**
     * Fill follower object
     *
     * @param $object
     * @param $follower_id
     * @param $followed_id
     * @return mixed
     */
    private function fillFollowerObject($object, $follower_id, $followed_id)
    {
        if($follower_id)
        {
            $object->follower_id = $follower_id;
        }

        if($followed_id)
        {
            $object->followed_id = $followed_id;
        }

        return $object;
    }
}