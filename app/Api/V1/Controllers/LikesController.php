<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\LikePaginationRequest;
use App\Api\V1\Requests\LikeRequest;
use App\Interfaces\LikeRepositoryInterface;
use App\Like;

class LikesController extends ApiController
{
    public function index(LikePaginationRequest $request, LikeRepositoryInterface $likeRepository)
    {
        $users = $likeRepository->usersFromLikes(
          $request->likable_id,
          $request->likable_type,
          $request->amount,
          $request->page
        );

        return $this->respondWithData($users);
    }

    public function store(LikeRequest $request)
    {
        $likeData = $request->only(['likable_id', 'likable_type']);
        $likeData['user_id'] = $this->authUser()->id;

        $like = Like::create($likeData);

        return $this->respondWithData($like);
    }

    public function destroy(Like $like)
    {
        if (!$this->belongsToAuthUser($like)) {
            return $this->respondForbidden('This like is not yours!');
        }

        if (!$like->delete()) {
            return $this->respondInternalError('Failed to delete like');
        }

        return $this->respondSuccess();
    }
}