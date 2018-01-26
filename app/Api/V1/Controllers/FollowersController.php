<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\FollowRequest;
use App\Events\NewFollower;
use App\User;
use Auth;
use Tymon\JWTAuth\JWTAuth;
use Dingo\Api\Http\Request;
use App\Interfaces\FollowerRepositoryInterface;
use App\Validators\Follower\CreateFollowValidator;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;

class FollowersController extends ApiController
{
    /**
     * @var FollowerRepositoryInterface
     */
    private $followers;

    /**
     * @var CreateFollowValidator
     */
    private $createFollowValidator;

    /**
     * @var JWTAuth
     */
    private $jwtAuth;

    public function __construct(JWTAuth $jwtAuth, FollowerRepositoryInterface $followers, CreateFollowValidator $createFollowValidator)
    {
        $this->jwtAuth = $jwtAuth;
        $this->followers = $followers;
        $this->createFollowValidator = $createFollowValidator;
    }

    /**
     * Get followers for authenticated user
     *
     * @return mixed
     */
    public function getFollowers()
    {
        $user = Auth::user();
        return $this->followers->getFollowers($user->id);
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
        $followedId = $request->get('followed_id');

//        if(!$this->createFollowValidator->passes())
//        {
//            return $this->respondWrongArgs($this->createFollowValidator->errors);
//        }

        //check if user exists
        if(!$this->followers->userExists($followedId))
        {
            return $this->respondNotFound('User does not exist');
        }

        $followerId = Auth::user()->id;

        //check if the following already exists
        if($this->followers->followExists($followerId, $followedId))
        {
            return $this->respondWrongArgs('You already follow this user.');
        }

        //check if dude try to follow himself
        if($followerId == $followedId)
        {
            return $this->respondWrongArgs('You already follow yourself.');
        }

        $follow = $this->followers->follow($followerId, $followedId);

        if ($follow) {

            // broadcast the event for notifications...
//            $followedUser = (new UserRepository(User::find($followedId)))->getById($followedId);
//            $followedUser = User::find($followedId);
//            event(new NewFollower($followedUser, $this->jwtAuth), $this->jwtAuth);

            if(env('SEND_NOTIFICATION_ON_FOLLOW')){
                event(new NewFollower(User::find($followedId), $this->jwtAuth), $this->jwtAuth);
            }

            return $follow;
        }

        return $this->respondInternalError('There was an error while trying to follow this user.');

    }

    /**
     * Unfollow another user
     *
     * @param FollowRequest $request
     *
     * @return bool|mixed
     * @internal param $followed_id
     */
    public function unfollow(FollowRequest $request)
    {
        $userId = Auth::user()->id;
        $followedId = $request->get('followed_id');

        if (!$this->followers->followExists($userId, $followedId)) {
            return $this->respondNotFound('You are not following this user.');
        }

        $unfollowSuccessful = $this->followers->unfollow($userId, $followedId);
        if ($unfollowSuccessful) {
            return $this->respondSuccess('You no longer follow this user.');
        }

        return false;
    }
}