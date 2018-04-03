<?php

namespace App\Api\V1\Controllers;

use App\Hashtag;
use App\Interfaces\MediaRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\User;
use Illuminate\Http\Request;

class SearchController extends ApiController
{
    public function searchUsername(Request $request, UserRepositoryInterface $userRepository, MediaRepositoryInterface $mediaRepository)
    {
        $this->validate($request, [
            'q' => 'required|string|min:3|max:100'
        ]);
        $query = $request->q;

        $users = User::where('username', 'LIKE', "%$query%")->get(['id', 'username', 'image']);

        if ($users && $authUser = $this->authUser()) {
            $userRepository->addIsFollowed($users, $authUser->id);
        }

        $mediaRepository->addThumbsToUsers($users);

        return $this->respondWithData($users);

    }

    public function searchHashtag(Request $request)
    {
        $this->validate($request, [
            'q' => 'required|string|min:3|max:100'
        ]);
        $query = $request->q;

        if (starts_with($query, '#')) {
            $query = substr($query, 1);
        }

        $hashtags = Hashtag::where('name', 'LIKE', "%$query%")->get(['name'])->pluck('with_hash');

        return $this->respondWithData($hashtags);
    }
}