<?php

use Illuminate\Database\Seeder;

class LikesTableSeeder extends Seeder
{
    public function run()
    {
        $likes = [
          [
            'user_id' => 1,
            'likable_id' => 1,
            'likable_type' => 2,
          ],
          [
            'user_id' => 2,
            'likable_id' => 1,
            'likable_type' => 2,
          ],
          [
            'user_id' => 1,
            'likable_id' => 1,
            'likable_type' => 1,
          ],
          [
            'user_id' => 2,
            'likable_id' => 1,
            'likable_type' => 1,
          ],
        ];

        foreach ($likes as $like) {
            DB::table('likes')->insert($like);
        }
    }
}