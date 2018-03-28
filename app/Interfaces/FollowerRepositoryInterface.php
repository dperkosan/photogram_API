<?php

namespace App\Interfaces;


interface FollowerRepositoryInterface
{
    /**
     * Get all followers by logged in user id
     *
     * @param $amount
     * @param $page
     * @param $id
     *
     * @return mixed
     */
    public function getFollowers($amount, $page, $id);

    /**
     * Get all followings by logged in user id
     *
     * @param $id
     *
     * @return mixed
     */
    public function getFollowings($id);

    /**
     * Follow user
     *
     * @param $followerId
     * @param $followedId
     * @return mixed
     */
    public function follow($followerId, $followedId);

    /**
     * Unfollow user
     *
     * @param $followerId
     * @param $followedId
     * @return mixed
     */
    public function unfollow($followerId, $followedId);

    /**
     * @param $user_id
     *
     * @return mixed
     */
    public function userExists($user_id);

    /**
     * @param $user_id
     *
     * @return mixed
     */
    public function followExists($followerId, $followedId);
}