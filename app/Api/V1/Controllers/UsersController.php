<?php
namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\UserRequest;
use App\Api\V1\Traits\ThumbsTrait;
use App\Interfaces\MediaRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

class UsersController extends ApiController
{
    use ThumbsTrait;

    protected $users;

    public function __construct(UserRepositoryInterface $users)
    {
        $this->users = $users;
    }

    public function updateAuthUser(UserRequest $request)
    {
        $updateData = [];

        $user = $this->authUser();

        $requestUsername = $request->username;

        // Handle updating username
        if ($requestUsername && $requestUsername !== $user->username) {
            $this->validate($request, [
              'username' => 'required|max:25|unique:users,username,' . $user->id,
            ]);
            $updateData['username'] = $requestUsername;
        }

        $requestData = $request->only([
          'name', 'gender_id', 'phone', 'about'
        ]);

        $updateData = array_merge($updateData, $requestData);

        if (!$updateData) {
            return $this->respondWrongArgs('Nothing to update.');
        }

        if (!$user->update($updateData)) {
            return $this->respondInternalError('Could not update user.');
        }

        return $this->respondWithData($user);
    }

    public function getAuthUser(MediaRepositoryInterface $mediaRepo)
    {
        $user = $this->authUser();

        if (!$user) {
            return $this->respondForbidden();
        }

        $this->users->addCounts($user);
        $mediaRepo->addThumbsToUsers($user);

        return $this->respondWithData($user);
    }

    public function exists(Request $request)
    {
        $exists = $this->users->existsWhere($request->only([
          'username', 'email', 'name', 'gender_id'
        ]));
        return $this->respondWithData(['exists' => $exists]);
    }

    public function find(Request $request, JWTAuth $JWTAuth, MediaRepositoryInterface $mediaRepo)
    {
        if ($request->id) {
            $user = $this->users->findById($request->id);
        } else {
            $user = $this->users->findWhere($request->only([
                'username', 'email', 'name', 'gender_id'
            ]));
        }

        if ($user) {
            $this->users->addCounts($user);
            $mediaRepo->addThumbsToUsers($user);
            if ($authUser = $JWTAuth->authenticate($JWTAuth->getToken())) {
                $this->users->addIsFollowed($user, $authUser->id);
            }
        }

        return $this->respondWithData($user);
    }

    /*
     * 2018-03-21 not used atm
     */
    public function show($user, JWTAuth $JWTAuth, MediaRepositoryInterface $mediaRepo)
    {
        $user = User::find($user);

        if ($user) {
            $this->users->addCounts($user);
            $mediaRepo->addThumbsToUsers($user);
            if ($authUser = $JWTAuth->authenticate($JWTAuth->getToken())) {
                $this->users->addIsFollowed($user, $authUser->id);
            }
        }

        return $this->respondWithData($user);
    }

    public function updateAuthProfileImage(Request $request, MediaRepositoryInterface $mediaRepo)
    {
        $this->validate($request, [
          'image' => 'required|image'
        ]);

        $image = $request->file('image');
        $user = $this->authUser();

        $mediaRepo->deleteFiles($user->image);
        $imagePath = $mediaRepo->saveUserImage($image, $user);

        if ($user->image !== $imagePath) {
            $user->image = $imagePath;
            $user->save();
        }
        $mediaRepo->addThumbsToUsers($user);

        return $this->respondWithData(['image' => $user->image]);
    }
}
