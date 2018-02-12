<?php

return [

  'sign_up' => [
    'release_token' => env('SIGN_UP_RELEASE_TOKEN'),
  ],

  'reset_password' => [
    'release_token' => env('PASSWORD_RESET_RELEASE_TOKEN', false),
  ],

  'allowed' => [
    'formats' => [
        'image' => ['jpg', 'jpeg', 'png'],
        'video' => ['mp4', 'wmv', 'flv', 'avi'],
    ],
  ],

  'thumbs' => [
      'post' => [
          'small'     => [107, 107],
          'medium'    => [320, 320],
          'large'     => [500, 500],
          ],
      'user' => [
          'avatar'          => [44, 44],
          'comment'         => [18, 18],
          'profile'         => [125, 125],
          'profile_large'   => [220, 220],
          ],
      ],

];
