<?php

namespace App\Validators\Follower;


use App\Validators\Validator;

class CreateFollowValidator extends Validator
{
    public static $rules = [
        'followed_id' => 'required|numeric'
    ];

    public static $messages = array();
}