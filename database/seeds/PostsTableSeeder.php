<?php

use Illuminate\Database\Seeder;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1Posts = [];
        $images1 = [
          'post/1/00-best-background.jpg',
          'post/1/1d1261130ad.jpg',
          'post/1/1440x720.jpg',
          'post/1/4732506-images-for-wallpaper.jpg',
        ];

        foreach ($images1 as $image) {
            $user1Posts[] = [
              'user_id' => 1,
              'media' => $image,
              'type_id' => 1,
              'description' => str_random(20),
          ];
        }

        $user2Posts = [];
        $images2 = [
          'post/2/c40aa0d.jpg',
          'post/2/mobile-wallpaper-13-610x1084.jpg',
          'post/2/photo-144526.jpeg',
        ];

        foreach ($images2 as $image) {
            $user2Posts[] = [
              'user_id' => 2,
              'media' => $image,
              'type_id' => 1,
              'description' => str_random(20),
            ];
        }

        $posts = array_merge($user1Posts, $user2Posts);

        foreach ($posts as $post) {
            DB::table('posts')->insert($post);
        }
    }
}
