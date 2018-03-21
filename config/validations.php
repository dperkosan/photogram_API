<?php

return [

    'sign_up' => [
        'email'                 => 'required|email|max:100|unique:users',
        'name'                  => 'nullable|max:100',
        'username'              => 'required|max:25|unique:users',
        'password'              => 'required|min:6|max:60|confirmed',
        'password_confirmation' => 'required',
    ],

    // No special validation for login here.
    // All validation logic is in the controller
    'login'   => [
        'email'    => 'required',
        'password' => 'required',
    ],

    'forgot_password' => [
        'email' => 'required|email|exists:users',
    ],

    'reset_password' => [
        'token'                 => 'required',
        'email'                 => 'required|email|exists:users',
        'password'              => 'required|min:6|confirmed',
        'password_confirmation' => 'required',
    ],

    'follow' => [
        'user_id' => 'required|integer',
    ],

    'user' => [
        'name'      => 'max:100',
        'gender_id' => 'integer|between:1,3',
    ],

    'post' => [
        'image'     => 'required_without:video|image',
        'video'     => 'required_without:image|mimes:mp4,flv,wmv,avi,mpeg,qt',
        'thumbnail' => 'image',
    ],

    'post_pagination' => [
        'amount'   => 'required|max:100',
        'page'     => 'required|max:50',
        'user_id'  => 'nullable',
        'username' => 'nullable',
    ],

    'comment_pagination' => [
        'amount'     => 'required|max:100',
        'page'       => 'required|max:50',
        'post_id'    => 'required|integer',
        'comment_id' => 'nullable|integer',
    ],

    'like' => [
        'likable_id'   => 'required|integer',
        'likable_type' => 'required|integer',
    ],

    'comment' => [
        'body'       => 'required|max:255',
        'post_id'    => 'required|integer',
        'comment_id' => 'nullable|integer',
    ],

    'like_pagination' => [
        'likable_id'   => 'required|integer',
        'likable_type' => 'required|integer',
        'amount'       => 'required|max:100',
        'page'         => 'required|max:50',
    ],

    'follower_pagination' => [
        'user_id' => 'required|integer',
        'amount'  => 'required|max:100',
        'page'    => 'required|max:50',
    ],

];
