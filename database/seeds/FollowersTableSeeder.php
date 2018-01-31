<?php

use App\User;
use Illuminate\Database\Seeder;

class FollowersTableSeeder extends Seeder
{
    public function run()
    {
        $allUserIds = collect(User::pluck('id')->toArray());

        $allFollows = [];

        // Tried 5000 didn't work
        $numberOfFollowsToInsert = 2000;

        $i = 0;
        while ($i < $numberOfFollowsToInsert) {

            $followerId = $allUserIds->random();
            $followedId = $allUserIds->random();

            $newFollow = [
              'follower_id' => $followerId,
              'followed_id' => $followedId,
            ];

            if (in_array($newFollow, $allFollows) || $followerId === $followedId) {
                continue;
            }

            $allFollows[] = $newFollow;
            $i++;
        }

        DB::table('followers')->insert($allFollows);
    }
}