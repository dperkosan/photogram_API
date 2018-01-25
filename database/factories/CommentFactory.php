<?php

use App\Comment;
use Faker\Generator;

$factory->define(Comment::class, function (Generator $faker) {
    return [
      'body' => $faker->text(255),
      'user_id' => 1,
      'post_id' => 1,
      'comment_id' => 3,
    ];

});