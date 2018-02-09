<?php

namespace App\Interfaces;


use Illuminate\Database\Eloquent\Collection;

interface LikeRepositoryInterface
{

    /**
     * Adds auth_like_id (by auth user) to every post and nested comment
     *
     * @param Collection $posts
     * @param integer    $userId user id of the auth user
     *
     * @return Collection $posts
     */
    public function addAuthLikeToPosts($posts, $userId);

    /**
     * Adds auth_like_id (by auth user) to every comment
     *
     * @param Collection $comments
     * @param integer    $userId user id of the auth user
     *
     * @return Collection $posts
     */
    public function addAuthLikeToComments($comments, $userId);
}