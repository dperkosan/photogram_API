<?php

namespace App\Validators\Post;


use App\Validators\Validator;

class CreatePostValidator extends Validator
{
    public static $rules = [
        'user_id' => 'required|numeric'
    ];

    public static $messages = array();
}