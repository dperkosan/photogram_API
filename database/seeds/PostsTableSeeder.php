<?php

use App\Post;
use App\User;

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

        $allUserIds = User::pluck('id');

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
        $posts = [];
        $images1 = [
          'images/post/2/',
          'images/post/2/',
          'images/post/2/',
          'images/post/2/',``
        ];

        foreach ($images1 as $image) {
            $posts[] = [
              'user_id'     => 2,
              'media'       => $image,
              'type_id'     => Post::TYPE_IMAGE,
              'description' => str_random(20),
            ];
        }

        DB::table('posts')->insert($posts);
    }
}
