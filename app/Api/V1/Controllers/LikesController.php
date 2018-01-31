<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\LikeRequest;
use App\Like;
use Illuminate\Http\Request;

class LikesController extends ApiController
{
    public function index(Request $request)
    {
        $likeData = $request->only(['likable_id', 'likable_type']);

        $usernames = \DB::table('likes')
          ->select('users.username')
          ->join('users', 'users.id', '=', 'likes.user_id')
          ->where($likeData)
          ->limit(5)
          ->pluck('username')
          ->toArray();

        $count = \DB::table('likes')->where($likeData)->count();

        $usernamesCount = count($usernames);

        $string = implode(',', $usernames);
        if ($count > $usernamesCount) {
            $diff = $count - $usernamesCount;
            $string .= ',and ' . $diff . ' more';
        }

        return $this->respondWithData(compact('usernames', 'count', 'string'));
    }

    public function store(LikeRequest $request)
    {
        $likeData = $request->only(['likable_id', 'likable_type']);

        $likeData['user_id'] = $this->authUser()->id;

        Like::create($likeData);

        return $this->respondSuccess();
    }

    public function destroy(LikeRequest $request)
    {
        $likeData = $request->only(['likable_id', 'likable_type']);

        $likeData['user_id'] = $this->authUser()->id;

        $like = Like::where($likeData)->first();

        if (!$this->belongsToAuthUser($like)) {
            return $this->respondForbidden('This like is not yours!');
        }

        if (!$like->delete()) {
            return $this->respondInternalError('Failed to delete like');
        }

        return $this->respondSuccess();
    }
}