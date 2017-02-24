<?php

namespace App\Validators;


class CreateUserValidator extends Validator
{
    public static $rules = [
        'name' => 'required|max:100',
        'username' => 'required|max:100',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed',
        'password_confirmation' => 'required',
    ];

    public static $messages = array();

}