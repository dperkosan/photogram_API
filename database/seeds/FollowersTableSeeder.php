<?php

use App\User;
use Illuminate\Database\Seeder;

class FollowersTableSeeder extends Seeder
{
    public function run()
    {
        $this->customSeed();

        $allUserIds = collect(User::pluck('id')->toArray());

        $allFollows = [];

        $i = 0;
        while ($i < 100) {

            $followerId = $allUserIds->random();
            $followedId = $allUserIds->random();

            $newFollow = [
              'follower_id'      => $followerId,
              'followed_id'   => $followedId,
            ];

            if (in_array($newFollow, $allFollows) || $followerId === $followedId) {
                continue;
            }

            $allFollows[] = $newFollow;
            $i++;
        }

        DB::table('followers')->insert($allFollows);
    }

    public function customSeed()
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