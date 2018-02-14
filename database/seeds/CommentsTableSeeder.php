<?php

use App\User;
use App\Post;

class CommentsTableSeeder extends BaseTableSeeder
{
    public function run()
    {
        $numberOfCommentsToSeed = 1000;

        $period = $this->getDatePeriod($numberOfCommentsToSeed);

        $faker = Faker\Factory::create();

        $allUserIds = collect(User::pluck('id')->toArray());

        $allPostIds = collect(Post::pluck('id')->toArray());

        $lastPostId = $allPostIds->last();

        $allComments = [];

        $allPosts = Post::with('comments')->get();

        foreach ($period as $date) {

            $userId = $allUserIds->random();
            $postId = $allPostIds->random();

            // If there is a comment on the post with $postId
            // then there is a 1 in 5 chance that this new comment
            // will be on another comment
            $commentId = null;
            $commentIds = $allPosts->where('id', $postId)->first()->comments->pluck('id');
            if ($commentIds->count() >= 1 && mt_rand(1, 5) === 5) {
                $commentId = $commentIds->random();
            }

            $formattedDate = $date->format('Y-m-d H:i:s');

            $allComments[] = [
              'body'       => $faker->text(255),
              'user_id'    => $userId,
              'post_id'    => $postId,
              'comment_id' => $commentId,
              'created_at' => $formattedDate,
            ];

            // 1000 comments on the last post.
            // We won't handle comment_id again as it is not important.
            // The goal is just to have many comments on one post.
            $allComments[] = [
              'body'       => $faker->text(255),
              'user_id'    => $userId,
              'post_id'    => $lastPostId,
              'comment_id' => null,
              'created_at' => $formattedDate,
            ];
        }

        DB::table('comments')->insert($allComments);

    }
}