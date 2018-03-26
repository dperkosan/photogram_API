<?php

use App\User;
use App\Post;

class CommentsTableSeeder extends BaseTableSeeder
{
    public function run()
    {
        // This is double because one comment gets this many comments additionally
        $numberOfCommentsToSeed = 1000;

        $period = $this->getDatePeriod($numberOfCommentsToSeed);

        $faker = Faker\Factory::create();

        $allUserIds = User::pluck('id');
        $allPostIds = Post::pluck('id');

        $lastPostId = $allPostIds->last();

        $allComments = [];

        foreach ($period as $date) {

            $userId = $allUserIds->random();
            $postId = $allPostIds->random();

            // Every 5-th comment is a reply to some random user
            $commentReplyUserId = null;
            if (mt_rand(1, 5) === 5) {
                $commentReplyUserId = $allUserIds->random();
            }

            $formattedDate = $date->format('Y-m-d H:i:s');

            $allComments[] = [
              'body'       => $faker->text(255),
              'user_id'    => $userId,
              'post_id'    => $postId,
              'reply_user_id' => $commentReplyUserId,
              'created_at' => $formattedDate,
            ];

            // 1000 comments on the last post.
            // We won't handle comment_id again as it is not important.
            // The goal is just to have many comments on one post.
            $allComments[] = [
              'body'       => $faker->text(255),
              'user_id'    => $userId,
              'post_id'    => $lastPostId,
              'reply_user_id' => null,
              'created_at' => $formattedDate,
            ];
        }

        DB::table('comments')->insert($allComments);

    }
}