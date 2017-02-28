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


    private $createFollowValidator;

    public function __construct(JWTAuth $jwtAuth, FollowerRepositoryInterface $followers, CreateFollowValidator $createFollowValidator)
    {
        $this->jwtAuth = $jwtAuth;
        $this->followers = $followers;
        $this->createFollowValidator = $createFollowValidator;
    }

    public function getFollowers()
    {
        $user = Auth::user();
        return $this->followers->getFollowers($user->id);
    }

    public function follow(Request $request)
    {
        //TODO: get followed user id
        $followedId = $request->get('followed_id');

        // validation
        if(!$this->createFollowValidator->passes())
        {
            return $this->respondWrongArgs($this->createFollowValidator->errors);
        }

        //TODO: check if the following already exists
        if($this->followers->followExists(Auth::user()->id, $followedId))
        {
            return $this->respondWrongArgs('You already follow this user.');
        }
        $followerId = Auth::user()->id;

        $follow = $this->followers->follow($followerId, $followedId);

        if($follow) return $follow;

        return $this->respondInternalError('There was an error while trying to follow this user.');


        //TODO: broadcast the event for notifications
    }


}