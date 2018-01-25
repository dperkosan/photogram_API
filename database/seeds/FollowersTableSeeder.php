<?php

use Illuminate\Database\Seeder;

class FollowersTableSeeder extends Seeder
{
    public function run()
    {
        $followers = [
          [
            'follower_id' => 2,
            'followed_id' => 1,
          ],
          [
            'follower_id' => 3,
            'followed_id' => 1,
          ],
          [
            'follower_id' => 4,
            'followed_id' => 1,
          ],
          [
            'follower_id' => 1,
            'followed_id' => 2,
          ],
          [
            'follower_id' => 3,
            'followed_id' => 2,
          ],
          [
            'follower_id' => 4,
            'followed_id' => 2,
          ],
        ];

        DB::table('followers')->insert($followers);
    }
}