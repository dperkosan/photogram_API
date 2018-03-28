<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\FollowerPaginationRequest;
use App\Api\V1\Requests\FollowRequest;
use App\Events\NewFollower;
use App\Interfaces\MediaRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\User;
use Auth;
use Tymon\JWTAuth\JWTAuth;
use Dingo\Api\Http\Request;
use App\Interfaces\FollowerRepositoryInterface;

class FollowersController extends ApiController
{
    /**
     * @var \App\Repositories\FollowerRepository
     */
    private $followers;

    /**
     * @var JWTAuth
     */
    private $jwtAuth;

    public function __construct(JWTAuth $jwtAuth, FollowerRepositoryInterface $followers)
    {
        $this->jwtAuth = $jwtAuth;
        $this->followers = $followers;
    }

    /**
     * Get followers for authenticated user
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function getFollowers(Request $request)
    {
        return $this->followers->getFollowers($request->amount, $request->page, $this->authUser()->id);
    }

    public function mutual(
        FollowerPaginationRequest $request,
        UserRepositoryInterface $userRepository,
        MediaRepositoryInterface $mediaRepo
    ) {
        $authUserId = $this->authUser()->id;
        $userIds[] = $authUserId;
        $userIds[] = $request->user_id;

        $users = $userRepository->usersMutualFollowers($userIds, $request->amount, $request->page);

        $mediaRepo->addThumbsToUsers($users);
        $userRepository->addIsFollowed($users, $authUserId);

        return $this->respondWithData($users);
    }

    /**
     * Follow another user
     *
     * @param FollowRequest|Request $request
     *
     * @return mixed
     */
    public function follow(FollowRequest $request)
    {
        $followedId = $request->get('user_id');
        $user = User::find($followedId);
        // Check if user exists
        if (!$user) {
            return $this->respondNotFound('User does not exist');
        }

        $followerId = $this->authUser()->id;

        // Check if the following already exists
        if ($this->followers->followExists($followerId, $followedId)) {
            return $this->respondWrongArgs('You already follow this user.');
        }

        // Check if dude try to follow himself
        if ($followerId == $followedId) {
            return $this->respondWrongArgs('You already follow yourself.');
        }

        $follow = $this->followers->follow($followerId, $followedId);

        if (!$follow) {
            return $this->respondInternalError('There was an error while trying to follow this user.');
        }

        // broadcast the event for notifications...
//            $followedUser = (new UserRepository(User::find($followedId)))->getById($followedId);
//            $followedUser = User::find($followedId);
//            event(new NewFollower($followedUser, $this->jwtAuth), $this->jwtAuth);

        if (env('SEND_NOTIFICATION_ON_FOLLOW')) {
            event(new NewFollower($user, $this->jwtAuth), $this->jwtAuth);
        }

        return $this->respondSuccess();

    }

    /**
     * Unfollow another user
     *
     * @param $user_id
     *
     * @return bool|mixed
     */
    public function unfollow($user_id)
    {
        $userId = $this->authUser()->id;
        $followedId = $user_id;

        if (!$this->followers->followExists($userId, $followedId)) {
            return $this->respondWrongArgs('You are not following this user.');
        }

        $unfollowSuccessful = $this->followers->unfollow($userId, $followedId);
        if ($unfollowSuccessful) {
            return $this->respondWithMessage('You no longer follow this user.');
        }

        return false;
    }
}