<?php

return [

    'sign_up' => [
        'release_token' => env('SIGN_UP_RELEASE_TOKEN'),
        'validation_rules' => [
          'email' => 'required|email|max:100|unique:users',
          'name' => 'required|max:100',
          'username' => 'required|max:100|unique:users',
          'password' => 'required|min:8|confirmed',
          'password_confirmation' => 'required',
        ]
    ],

    'login' => [
        'validation_rules' => [
            'email' => 'required|email',
            'password' => 'required'
        ]
    ],

    'forgot_password' => [
        'validation_rules' => [
            'email' => 'required|email'
        ]
    ],

    'reset_password' => [
        'release_token' => env('PASSWORD_RESET_RELEASE_TOKEN', false),
        'validation_rules' => [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed'
        ]
    ],

    'follow' => [
      'validation_rules' => [
        'followed_id' => 'required|integer'
      ]
    ],

];
