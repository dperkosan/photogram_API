<?php

namespace App\Api\V1\Controllers;

use Auth;
use Tymon\JWTAuth\JWTAuth;
use Dingo\Api\Http\Request;
use App\Interfaces\FollowerRepositoryInterface;
use App\Validators\Follower\CreateFollowValidator;

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
     * @param Request $request
     * @return json|mixed
     */
    public function follow(Request $request)
    {
        //get followed user id
        $followedId = $request->get('followed_id');

        // validation
        if(!$this->createFollowValidator->passes())
        {
            return $this->respondWrongArgs($this->createFollowValidator->errors);
        }

        //check if user exists
        if(!$this->followers->userExists($followedId))
        {
            return $this->respondNotFound('User does not exist');
        }

        //check if the following already exists
        if($this->followers->followExists(Auth::user()->id, $followedId))
        {
            return $this->respondWrongArgs('You already follow this user.');
        }
        $followerId = Auth::user()->id;

        $follow = $this->followers->follow($followerId, $followedId);

        if($follow) return $follow;

        return $this->respondInternalError('There was an error while trying to follow this user.');

        //TODO: broadcast the event for notifications...

    }

    /**
     * Unfollow another user
     *
     * @param $followed_id
     * @return json|bool|mixed
     */
    public function unfollow($followed_id)
    {
        $user_id = Auth::user()->id;
        if($this->followers->followExists($user_id, $followed_id))
        {
            $follow = $this->followers->unfollow($user_id, $followed_id);
            if($follow) return $this->respondSuccess('You no longer follow this user.');

            return false;
        }

        return $this->respondNotFound('You are not following this user.');
    }


}