<?php

use App\Like;
use App\Post;
use App\User;
use App\Comment;
use Illuminate\Database\Seeder;

class LikesTableSeeder extends Seeder
{
    public function run()
    {
//        $this->customSeed();

        $faker = Faker\Factory::create();

        $allUserIds = collect(User::pluck('id')->toArray());

        // Only one fourth of the posts are cool and they will get all the likes
        $allPostIds = collect(Post::pluck('id')->toArray());
        $allPostIds = $allPostIds->random($allPostIds->count() / 4);

        // Only 1 / 10 comments are cool and they will get likes
        $allCommentIds = collect(Comment::pluck('id')->toArray());
        $allCommentIds = $allCommentIds->random($allCommentIds->count() / 10);

        $this->seed($allUserIds, $allPostIds, Like::LIKABLE_POST, 1000);
        $this->seed($allUserIds, $allCommentIds, Like::LIKABLE_COMMENT, 1000);
    }

    public function seed($allUserIds, $allLikableIds, $likableType, $amount)
    {
        $allLikes = [];

        $i = 0;
        while ($i < $amount) {

            $userId = $allUserIds->random();
            $likableId = $allLikableIds->random();

            $newLike = [
              'user_id'      => $userId,
              'likable_id'   => $likableId,
            ];

            if (in_array($newLike, $allLikes)) {
                continue;
            }

            $allLikes[] = $newLike;
            $i++;
        }

        foreach ($allLikes as &$oneLike) {
            $oneLike['likable_type'] = $likableType;
        }

        DB::table('likes')->insert($allLikes);
    }

    public function customSeed()
    {
        $likes = [
          [
            'user_id'      => 1,
            'likable_id'   => 1,
            'likable_type' => Like::LIKABLE_COMMENT,
          ],
          [
            'user_id'      => 2,
            'likable_id'   => 1,
            'likable_type' => Like::LIKABLE_COMMENT,
          ],
          [
            'user_id'      => 1,
            'likable_id'   => 1,
            'likable_type' => Like::LIKABLE_POST,
          ],
          [
            'user_id'      => 2,
            'likable_id'   => 1,
            'likable_type' => Like::LIKABLE_POST,
          ],
          [
            'user_id'      => 3,
            'likable_id'   => 1,
            'likable_type' => Like::LIKABLE_POST,
          ],
        ];

        DB::table('likes')->insert($likes);
    }
}