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

    protected $userRepository;
    protected $mediaRepository;

    public function __construct(UserRepositoryInterface $userRepository, MediaRepositoryInterface $mediaRepository)
    {
        $this->userRepository = $userRepository;
        $this->mediaRepository = $mediaRepository;
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
            return $this->respondInternalError('Failed to update user.');
        }

        $this->userRepository->addCounts($user);
        $this->mediaRepository->addThumbsToUsers($user);

        return $this->respondWithData($user);
    }

    public function getAuthUser()
    {
        $user = $this->authUser();

        if (!$user) {
            return $this->respondForbidden();
        }

        $this->userRepository->addCounts($user);
        $this->mediaRepository->addThumbsToUsers($user);

        return $this->respondWithData($user);
    }

    public function exists(Request $request)
    {
        $exists = $this->userRepository->existsWhere($request->only([
          'username', 'email', 'name', 'gender_id'
        ]));
        return $this->respondWithData(['exists' => $exists]);
    }

    public function find(Request $request, JWTAuth $JWTAuth)
    {
        if ($request->id) {
            $user = $this->userRepository->findById($request->id);
        } else {
            $user = $this->userRepository->findWhere($request->only([
                'username', 'email', 'name', 'gender_id'
            ]));
        }

        if ($user) {
            $this->userRepository->addCounts($user);
            $this->mediaRepository->addThumbsToUsers($user);
            if ($authUser = $JWTAuth->authenticate($JWTAuth->getToken())) {
                $this->userRepository->addIsFollowed($user, $authUser->id);
            }
        }

        return $this->respondWithData($user);
    }

    /*
     * 2018-03-21 not used atm
     */
    public function show($user, JWTAuth $JWTAuth)
    {
        $user = User::find($user);

        if ($user) {
            $this->userRepository->addCounts($user);
            $this->mediaRepository->addThumbsToUsers($user);
            if ($authUser = $JWTAuth->authenticate($JWTAuth->getToken())) {
                $this->userRepository->addIsFollowed($user, $authUser->id);
            }
        }

        return $this->respondWithData($user);
    }

    public function updateAuthProfileImage(Request $request)
    {
        $this->validate($request, [
          'image' => 'required|image'
        ]);

        $image = $request->file('image');
        $user = $this->authUser();

        $this->mediaRepository->deleteFiles($user->image);
        $imagePath = $this->mediaRepository->saveUserImage($image, $user);

        if ($user->image !== $imagePath) {
            $user->image = $imagePath;
            $user->save();
        }
        $this->mediaRepository->addThumbsToUsers($user);

        return $this->respondWithData(['image' => $user->image]);
    }
}
