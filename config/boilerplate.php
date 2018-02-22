<?php

return [

  'sign_up' => [
    'release_token' => env('SIGN_UP_RELEASE_TOKEN'),
  ],

  'reset_password' => [
    'release_token' => env('PASSWORD_RESET_RELEASE_TOKEN', false),
  ],

  'thumbs' => [
    'post' => [
      'small'  => [107, 107],
      'medium' => [320, 320],
      'large'  => [500, 500],
    ],
    'user' => [
      'avatar'        => [44, 44],
      'comment'       => [28, 28],
      'profile'       => [125, 125],
      'profile_large' => [220, 220],
    ],
  ],

  'default_user_images' => [
    'avatar' => 'images/user/default-avatar.jpg',
    'comment' => 'images/user/default-comment.jpg',
    'profile' => 'images/user/default-profile.jpg',
    'profile_large' => 'images/user/default-profile_large.jpg',
    'orig' => 'images/user/default-orig.jpg',
  ],

  'test_user' => [
    'username'  => 'test.user',
    'name'      => 'Test User',
    'email'     => 'test.user@example.com',
    'password'  => '12345',
    'gender_id' => 1,
    'phone'     => '123456',
    'about'     => 'about me something',
    'image'     => 'images/user/placeholder-[~FORMAT~].jpg',
    'active'    => 1,
    'type_id'   => 1,
  ],

];
