<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\FollowerPaginationRequest;
use App\Api\V1\Requests\MutualFollowerPaginationRequest;
use App\Api\V1\Requests\FollowRequest;
use App\Interfaces\MediaRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\User;
use App\Interfaces\FollowerRepositoryInterface;

class FollowersController extends ApiController
{
    private $followerRepository;

    public function __construct(FollowerRepositoryInterface $followerRepository)
    {
        $this->followerRepository = $followerRepository;
    }

    public function getFollowers(FollowerPaginationRequest $request, MediaRepositoryInterface $mediaRepository, UserRepositoryInterface $userRepository)
    {
        $users = $this->followerRepository->getFollowers($this->authUser()->id, $request->amount, $request->page);

        $mediaRepository->addThumbsToUsers($users);

        $userRepository->addIsFollowed($users, $this->authUser()->id);

        return $this->respondWithData($users);
    }

    public function getFollowings(FollowerPaginationRequest $request, MediaRepositoryInterface $mediaRepository, UserRepositoryInterface $userRepository)
    {
        $users = $this->followerRepository->getFollowings($this->authUser()->id, $request->amount, $request->page);

        $mediaRepository->addThumbsToUsers($users);

        $userRepository->addIsFollowed($users, $this->authUser()->id);

        return $this->respondWithData($users);
    }

    public function getMutual(MutualFollowerPaginationRequest $request, FollowerRepositoryInterface $followerRepository, UserRepositoryInterface $userRepository, MediaRepositoryInterface $mediaRepository)
    {
        $authUserId = $this->authUser()->id;
        $userIds[] = $authUserId;
        $userIds[] = $request->user_id;

        $users = $followerRepository->getMutualFollowers($userIds, $request->amount, $request->page);

        $mediaRepository->addThumbsToUsers($users);
        $userRepository->addIsFollowed($users, $authUserId);

        return $this->respondWithData($users);
    }

    public function follow(FollowRequest $request)
    {
        $followedId = $request->user_id;
        $user = User::find($followedId);
        // Check if user exists
        if (!$user) {
            return $this->respondNotFound('User does not exist');
        }

        $followerId = $this->authUser()->id;

        // Check if the following already exists
        if ($this->followerRepository->followExists($followerId, $followedId)) {
            return $this->respondWrongArgs('You already follow this user.');
        }

        // Check if dude try to follow himself
        if ($followerId == $followedId) {
            return $this->respondWrongArgs('You already follow yourself.');
        }

        $follow = $this->followerRepository->follow($followerId, $followedId);

        if (!$follow) {
            return $this->respondInternalError('Failed to follow this user.');
        }

        // broadcast the event for notifications...
//            $followedUser = (new UserRepository(User::find($followedId)))->getById($followedId);
//            $followedUser = User::find($followedId);
//            event(new NewFollower($followedUser, $this->jwtAuth), $this->jwtAuth);

//        if (env('SEND_NOTIFICATION_ON_FOLLOW')) {
//            event(new NewFollower($user, $this->jwtAuth), $this->jwtAuth);
//        }

        return $this->respondSuccess();

    }

    public function unfollow($user_id)
    {
        $userId = $this->authUser()->id;
        $followedId = $user_id;

        if (!$this->followerRepository->followExists($userId, $followedId)) {
            return $this->respondWrongArgs('You are not following this user.');
        }

        $success = $this->followerRepository->unfollow($userId, $followedId);
        if (!$success) {
            return $this->respondInternalError('Failed to unfollow user');
        }

        return $this->respondWithMessage('You no longer follow this user.');
    }
}