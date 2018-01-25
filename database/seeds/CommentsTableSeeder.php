<?php

use Illuminate\Database\Seeder;

class CommentsTableSeeder extends Seeder
{
    public function run()
    {
        $comments = [
          [
            'body' => 'Sexy lady',
            'user_id' => 1,
            'post_id' => 1,
            'comment_id' => null,
          ],
          [
            'body' => 'Indeed',
            'user_id' => 2,
            'post_id' => 1,
            'comment_id' => null,
          ],
          [
            'body' => 'Shame shame shame',
            'user_id' => 2,
            'post_id' => 1,
            'comment_id' => 1,
          ],
          [
            'body' => 'sladjanaaaaaa',
            'user_id' => 1,
            'post_id' => 1,
            'comment_id' => 3,
          ],
          [
            'body' => 'Gangnam style',
            'user_id' => 3,
            'post_id' => 1,
            'comment_id' => 3,
          ],
        ];

        DB::table('comments')->insert($comments);
    }
}