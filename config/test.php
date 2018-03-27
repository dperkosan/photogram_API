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
        'user_image' => $userImage,
        'auth_user' => [
            'id',
            'username',
            'name',
            'email',
            'gender_id',
            'phone',
            'about',
            'image' => $userImage,
            'posts_count',
            'followers_count',
            'following_count',
        ],
        'user'    => [
            'id',
            'username',
            'name',
            'email',
            'gender_id',
            'phone',
            'about',
            'image' => $userImage,
            'posts_count',
            'followers_count',
            'following_count',
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
            'created_at',
            'username',
            'user_image' => $userImage,
            'reply_user_id',
            'reply_user_username',
            'likes_count',
            'auth_like_id',
        ],
        'like'    => [
            // TODO: users from likes structure
        ],
    ],
];