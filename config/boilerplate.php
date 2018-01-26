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

];
