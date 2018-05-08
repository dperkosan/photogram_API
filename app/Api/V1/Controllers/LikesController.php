<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\LikePaginationRequest;
use App\Api\V1\Requests\LikeRequest;
use App\Interfaces\MediaRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Like;

class LikesController extends ApiController
{
    public function index(LikePaginationRequest $request, UserRepositoryInterface $userRepository, MediaRepositoryInterface $mediaRepo)
    {
        $users = $userRepository->usersFromLikes(
            $request->likable_id,
            $request->likable_type,
            $request->amount,
            $request->page
        );

        if ($users && $authUser = $this->authUser()) {
            $userRepository->addIsFollowed($users, $authUser->id);
        }

        $mediaRepo->addThumbsToUsers($users);

        return $this->respondWithData($users);
    }

    public function store(LikeRequest $request)
    {
        $this->dLogWithTime('Starting...');

        $likeData = $request->only(['likable_id', 'likable_type']);
        $likeData['user_id'] = $this->authUser()->id;

        $like = Like::create($likeData);
        $this->dLogWithTime('Like inserted in db');

        return $this->respondWithData($like);
    }

    public function destroy($like)
    {
        $like = Like::find($like);

        if (!$this->belongsToAuthUser($like)) {
            return $this->respondForbidden('This like is not yours!');
        }

        if (!$like->delete()) {
            return $this->respondInternalError('Failed to delete like');
        }

        return $this->respondSuccess();
    }
}