<?php

return [

  'sign_up' => [
      'email' => 'required|email|max:100|unique:users',
      'name' => 'required|max:100',
      'username' => 'required|max:25|unique:users',
      'password' => 'required|min:8|confirmed',
      'password_confirmation' => 'required',
  ],

  'login' => [
      'email' => 'required|email',
      'password' => 'required'
  ],

  'forgot_password' => [
      'email' => 'required|email'
  ],

  'reset_password' => [
      'token' => 'required',
      'email' => 'required|email',
      'password' => 'required|confirmed'
  ],

  'follow' => [
      'followed_id' => 'required|integer'
  ],

// 'user' validation is in the UserRequest because of the logic

];
