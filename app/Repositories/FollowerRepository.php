<?php

namespace App\Repositories;


use App\User;
use App\Follower;
use App\Interfaces\FollowerRepositoryInterface;

class FollowerRepository implements FollowerRepositoryInterface
{
    private $user;

    private $follower;

    public function __construct(User $user, Follower $follower)
    {
        $this->user = $user;
        $this->follower = $follower;
    }

    public function getFollowers($followedId)
    {
        $user = $this->user->find($followedId);
        return $user->followers()->get();
    }

    public function getFollowings($followerId)
    {
        $user = $this->user->find($followerId);
        return $user->followings()->get();
    }

    public function follow($followerId, $followedId)
    {
        $follow = $this->fillFollowerObject($this->follower, $followerId, $followedId);
        if($follow->save()) return $follow;

        return false;
    }

    public function followExists($followerId, $followedId)
    {
        return ($this->follower->where(['follower_id' => $followerId, 'followed_id' => $followedId])->count() > 0);
    }

    public function unfollow($followerId, $followedId)
    {
        $follow = $this->follower->where(['follower_id' => $followerId, 'followed_id' => $followedId])->first();
        if($follow->delete()) return true;

        return false;
    }

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