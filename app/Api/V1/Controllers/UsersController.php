<?php
namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\UserRequest;
use App\Api\V1\Traits\ThumbsTrait;
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

        if (!$user->update($updateData)) {
            return $this->respondInternalError('Could not update user.');
        }

        return $this->setStatusCode(204)->respond(['updatedData' => $updateData]);
    }

    public function getAuthUser()
    {
        $user = $this->authUser();

        if (!$user) {
            return $this->respondForbidden();
        }

        $this->users->addCounts($user);

        return $this->respondWithData($user);
    }

    public function exists(Request $request)
    {
        $exists = $this->users->existsWhere($request->only([
          'username', 'email', 'name', 'gender_id'
        ]));
        return $this->respondWithData(['exists' => $exists]);
    }

    public function find(Request $request, JWTAuth $JWTAuth)
    {
        $user = $this->users->findWhere($request->only([
          'username', 'email', 'name', 'gender_id'
        ]));

        if ($user) {
            $this->users->addCounts($user);
            if ($authUser = $JWTAuth->authenticate($JWTAuth->getToken())) {
                $this->users->addIsFollowed($user, $authUser->id);
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

//        $imageName = $image->getClientOriginalName();
        $imageName = "{$user->username}-orig.{$image->getClientOriginalExtension()}";

        $storage = \Storage::disk('public');
        if ($storage->exists($user->image)) {
            $storage->delete($user->image);
        }

        $path = "/images/user/{$user->id}";

        $imagePath = $storage->putFileAs($path, $image, $imageName);
        $imagePath = str_replace('-orig', '-[~FORMAT~]', $imagePath);

        // make some thumbs
        $this->makeThumbs($path, $imageName);



        if ($user->image !== $imagePath) {
            $user->image = $imagePath;
            $user->save();
        }

        return $this->respondWithData(['image' => $imagePath]);
    }
}
