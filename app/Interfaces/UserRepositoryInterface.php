<?php

namespace App\Interfaces;


interface UserRepositoryInterface
{
    /**
     * @return \App\User
     */
    public function addCounts($user);

//    public function addThumbs($users);

    public function addIsFollowed($users, $authUserId);

    public function usersFromLikes($likableId, $likableType, $amount, $page);

    /**
     * @return \App\User
     */
    public function findByEmail($email);

    /**
     * @return \App\User
     */
    public function findById($id);

    /**
     * Same arguments functionality as in the query where() function
     *
     * @param  string|array|\Closure  $column
     * @param  mixed   $operator
     * @param  mixed   $value
     * @param  string  $boolean
     *
     * @return \App\User
     */
    public function findWhere($column, $operator = null, $value = null, $boolean = 'and');

    /**
     * Same arguments functionality as in the query where() function
     */
    public function existsWhere($column, $operator = null, $value = null, $boolean = 'and');

    public function emailExists($email);

    public function usernameExists($username);

    public function store($data);

    public function sendConfirmEmailNotification($token);

    public function getUserIdFromUsername($username);
}