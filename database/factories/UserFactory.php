<?php

use App\User;
use Faker\Generator;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Generator $faker) {
    return [
      'name' => $faker->name,
      'username' => $faker->unique()->userName,
      'email' => $faker->unique()->safeEmail,
      'password' => bcrypt('admin'),
      'gender_id' => $faker->randomElement([1, 2, 3]),
      'phone' => $faker->phoneNumber,
      'about' => $faker->text(200),
      'image' => 'user/placeholder.png',
      'type_id' => 1,
      'active' => 1,
    ];
});