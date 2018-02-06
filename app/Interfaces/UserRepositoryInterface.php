<?php

namespace App\Interfaces;


interface UserRepositoryInterface
{
    public function findByEmail($email);

    public function findById($id);
    /**
     * Same arguments functionality as in the query where() function
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
}