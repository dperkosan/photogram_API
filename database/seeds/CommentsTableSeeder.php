<?php

use App\User;
use App\Post;
use App\Comment;
use Illuminate\Database\Seeder;

class CommentsTableSeeder extends Seeder
{
    public function run()
    {
//        $this->customSeeder();

        $faker = Faker\Factory::create();

        $allUserIds = collect(User::pluck('id')->toArray());

        $allPostIds = collect(Post::pluck('id')->toArray());

        $allComments = [];

        $allPosts = Post::with('comments')->get();

        foreach (range(1, 1000) as $index) {

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

            $allComments[] = [
              'body'       => $faker->text(255),
              'user_id'    => $userId,
              'post_id'    => $postId,
              'comment_id' => $commentId,
            ];
        }

        DB::table('comments')->insert($allComments);

    }

    private function customSeeder()
    {
        $comments = [
          [
            'body'       => 'Sexy lady',
            'user_id'    => 1,
            'post_id'    => 1,
            'comment_id' => null,
          ],
          [
            'body'       => 'Indeed',
            'user_id'    => 2,
            'post_id'    => 1,
            'comment_id' => null,
          ],
          [
            'body'       => 'Shame shame shame',
            'user_id'    => 2,
            'post_id'    => 1,
            'comment_id' => 1,
          ],
          [
            'body'       => 'sladjanaaaaaa',
            'user_id'    => 1,
            'post_id'    => 1,
            'comment_id' => 3,
          ],
          [
            'body'       => 'Gangnam style',
            'user_id'    => 3,
            'post_id'    => 1,
            'comment_id' => 3,
          ],
        ];

        DB::table('comments')->insert($comments);
    }
}