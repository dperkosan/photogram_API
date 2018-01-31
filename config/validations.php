<?php

return [

  'sign_up' => [
    'email'                 => 'required|email|max:100|unique:users',
    'name'                  => 'required|max:100',
    'username'              => 'required|max:25|unique:users',
    'password'              => 'required|min:5|confirmed',
    'password_confirmation' => 'required',
  ],

  'login' => [
    'email'    => 'required|email',
    'password' => 'required',
  ],

  'forgot_password' => [
    'email' => 'required|email',
  ],

  'reset_password' => [
    'token'    => 'required',
    'email'    => 'required|email',
    'password' => 'required|confirmed',
  ],

  'follow' => [
    'followed_id' => 'required|integer',
  ],

  'user' => [
    'name'      => 'required|max:100',
    'gender_id' => 'integer|between:1,3',
  ],

  'post' => [
    'image' => 'required_without:video|image',
    'video' => 'required_without:image|mimes:mp4,flv,wmv,avi,mpeg,qt',
    'thumbnail' => 'image',
  ],

  'post_data' => [
    'amount' => 'required|max:100',
    'page' => 'required|max:50',
  ],

  'like' => [
    'likable_id' => 'required|integer',
    'likable_type' => 'required|integer',
  ],

];
