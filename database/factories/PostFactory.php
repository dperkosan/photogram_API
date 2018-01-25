<?php

use App\Post;
use Faker\Generator;

$factory->define(Post::class, function (Generator $faker) {

    $num = mt_rand(1, 10);

    if ($num === 10) {
        $post['media'] = 'videos/post/placeholder.mp4';
        $post['type_id'] = 2;
        $post['thumbnail'] = 'images/thumbnail.png';
    } else {
        $post['media'] = 'images/post/placeholder.jpg';
        $post['type_id'] = 1;
    }

    $data = [
      'user_id' => 1, // OVERRIDE THIS WHEN CALLING factory() !!
      'type_id' => $post['type_id'],
      'media' => $post['media'],
      'description' => $faker->text(mt_rand(5, 200)),
      'deleted' => false,
    ];

    if (isset($post['thumbnail'])) {
        $data['thumbnail'] = $post['thumbnail'];
    }

    return $data;
});