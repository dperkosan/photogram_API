<?php

namespace App\Interfaces;


interface FollowerRepositoryInterface
{
    /**
     * Get all followers by logged in user id
     *
     * @param integer $id
     * @param integer $amount
     * @param integer $page
     *
     * @return mixed
     */
    public function getFollowers($id, $amount, $page);

    /**
     * Get all followings by logged in user id
     *
     * @param integer $id
     * @param integer $amount
     * @param integer $page
     *
     * @return mixed
     */
    public function getFollowings($id, $amount, $page);

    /**
     * Follow user
     *
     * @param integer $followerId
     * @param integer $followedId
     *
     * @return mixed
     */
    public function follow($followerId, $followedId);

    /**
     * Unfollow user
     *
     * @param integer $followerId
     * @param integer $followedId
     *
     * @return mixed
     */
    public function unfollow($followerId, $followedId);

    /**
     * @param integer $followerId
     * @param integer $followedId
     *
     * @return mixed
     */
    public function followExists($followerId, $followedId);

    /**
     * @param array   $userIds
     * @param integer $amount
     * @param integer $page
     *
     * @return mixed
     */
    public function getMutualFollowers(array $userIds, $amount, $page);

}