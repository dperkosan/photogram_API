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

    public function getMutualFollowers(array $userIds, $amount, $page)
    {
        $offset = $this->calcOffset($amount, $page);

        return $this->user
            ->select(['users.id', 'users.username', 'users.image'])
            ->join('followers', 'users.id', '=', 'followers.follower_id')
            ->whereIn('followers.follower_id', $userIds)
            ->groupBy('followers.follower_id')
            ->havingRaw('COUNT(`followers`.`follower_id`) >= ' . count($userIds))
            ->offset($offset)
            ->limit($amount)
            ->get();
    }

    /**
     * Get followers for authenticated user
     *
     * @param integer $followedId
     * @param integer $amount
     * @param integer $page
     *
     * @return mixed
     */
    public function getFollowers($followedId, $amount, $page)
    {
        return $this->getFollowersFollowings('follower_id', 'followed_id', $followedId, $amount, $page);
    }

    /**
     * Get followings for authenticated user
     *
     * @param integer $followerId
     * @param integer $amount
     * @param integer $page
     *
     * @return mixed
     */
    public function getFollowings($followerId, $amount, $page)
    {
        return $this->getFollowersFollowings('followed_id', 'follower_id', $followerId, $amount, $page);
    }

    /**
     * Helper function because getFollowers and getFollowings is pretty much the same
     *
     * @param string  $joinColumn
     * @param string  $whereClauseColumn
     * @param integer $id
     * @param integer $amount
     * @param integer $page
     *
     * @return mixed
     */
    protected function getFollowersFollowings($joinColumn, $whereClauseColumn, $id, $amount, $page)
    {
        $offset = $this->calcOffset($amount, $page);

        return $this->user
            ->select(['users.id', 'users.username', 'users.image'])
            ->join('followers', 'users.id', '=', "followers.$joinColumn")
            ->where("followers.$whereClauseColumn", $id)
            ->offset($offset)
            ->limit($amount)
            ->get();
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