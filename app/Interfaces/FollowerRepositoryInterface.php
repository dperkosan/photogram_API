<?php

namespace App\Interfaces;


interface FollowerRepositoryInterface
{
    /**
     * Get all followers by logged in user id
     *
     * @param $id
     * @return mixed
     */
    public function getFollowers($id);

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