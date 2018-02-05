<?php

namespace App\Interfaces;


interface UserRepositoryInterface
{
    /**
     * Same arguments functionality as in the query where() function
     */
    public function existsWhere($column, $operator = null, $value = null, $boolean = 'and');

    public function emailExists($email);

    public function usernameExists($username);

    public function store($data);

    public function sendConfirmEmailNotification($token);
}