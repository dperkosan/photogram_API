<?php

use App\Post;

class PostsTableSeeder extends BaseTableSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $numberOfPostsToSeed = 500;

        $period = $this->getDatePeriod($numberOfPostsToSeed);

        $faker = Faker\Factory::create();

        $allUserIds = collect(App\User::pluck('id')->toArray());

        $allPosts = [];

        foreach ($period as $date) {
            $num = mt_rand(1, 10);

            if ($num === 10) {
                $post['media'] = 'videos/post/placeholder.mp4';
                $post['type_id'] = Post::TYPE_VIDEO;
                $post['thumbnail'] = 'images/thumbnail.png';
            } else {
                $post['media'] = 'images/post/placeholder-[~FORMAT~].jpg';
                $post['type_id'] = POST::TYPE_IMAGE;
                $post['thumbnail'] = null;
            }

            $newPost = [
              'user_id'     => $allUserIds->random(),
              'type_id'     => $post['type_id'],
              'media'       => $post['media'],
              'description' => $faker->text(mt_rand(5, 200)),
              'thumbnail'   => $post['thumbnail'],
              'created_at'  => $date->format('Y-m-d H:i:s'),
            ];

            $allPosts[] = $newPost;
        }

        DB::table('posts')->insert($allPosts);

    }

    public function customSeeder()
    {
        $user1Posts = [];
        $images1 = [
          'images/post/1/00-best-background.jpg',
          'images/post/1/1d1261130ad.jpg',
          'images/post/1/1440x720.jpg',
          'images/post/1/4732506-images-for-wallpaper.jpg',
        ];

        foreach ($images1 as $image) {
            $user1Posts[] = [
              'user_id'     => 1,
              'media'       => $image,
              'type_id'     => Post::TYPE_IMAGE,
              'description' => str_random(20),
            ];
        }

        $user2Posts = [];
        $images2 = [
          'images/post/2/c40aa0d.jpg',
          'images/post/2/mobile-wallpaper-13-610x1084.jpg',
          'images/post/2/photo-144526.jpeg',
        ];

        foreach ($images2 as $image) {
            $user2Posts[] = [
              'user_id'     => 2,
              'media'       => $image,
              'type_id'     => Post::TYPE_IMAGE,
              'description' => str_random(20),
            ];
        }

        $posts = array_merge($user1Posts, $user2Posts);
        DB::table('posts')->insert($posts);
    }
}
