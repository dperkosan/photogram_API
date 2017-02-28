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
        $this->follower->follower_id = $followerId;
        $this->follower->followed_id = $followedId;
        $this->follower->save();

        return $this->follower;
    }

    public function followExists($followerId, $followedId)
    {
        return ($this->follower->where(['follower_id' => $followerId, 'followed_id' => $followedId])->count() > 0);
    }

    public function unfollow($followerId, $followedId)
    {

    }

    private function fillFollowerObject($object, $data)
    {
        if(isset($data['follower_id']))
        {
            $object->follower_id = $data['follower_id'];
        }

        if(isset($data['followed_id']))
        {
            $object->followed_id = $data['followed_id'];
        }

        return $object;
    }
}