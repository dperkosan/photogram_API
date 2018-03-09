<?php

$userImage = [
    'avatar',
    'comment',
    'profile',
    'profile_large',
    'placeholder',
    'orig',
];

//$media = [
//    'small',
//    'medium',
//    'large',
//    'placeholder',
//    'orig',
//];

return [
    'json_structure' => [
        'user'    => [
            // TODO: user structure
        ],
        'post'    => [
            'id',
            'user_id',
            'type_id',
            'media',
            'thumbnail',
            'description',
            'created_at',
            'username',
            'user_image' => $userImage,
            'comments_count',
            'likes_count',
            'auth_like_id',
            'comments',
        ],
        'comment' => [
            'id',
            'user_id',
            'post_id',
            'comment_id',
            'created_at',
            'username',
            'user_image' => $userImage,
            'likes_count',
            'auth_like_id',
        ],
        'like'    => [
            // TODO: users from likes structure
        ],
    ],
];