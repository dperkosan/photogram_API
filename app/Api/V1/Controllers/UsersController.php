<?php
namespace App\Api\V1\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends ApiController
{
    private function authUser()
    {
        return Auth::user();
    }

    public function updateAuthUser(Request $request)
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

        if ($user->update($updateData)) {
            return $this->setStatusCode(204)->respond(['updatedData' => $updateData]);
        }

        return $this->respondInternalError();
    }

    public function getAuthUser()
    {
        return $this->authUser();
    }

    public function checkUsername($username)
    {
        $exists = false;
        if (User::where('username', '=', $username)->exists()) {
            $exists = true;
        }
        return $this->respondWithData(['exists' => $exists]);
    }

    public function updateAuthProfileImage(Request $request)
    {
        if (!$request->hasFile('image')) {
            return $this->respondWrongArgs('Image param name needs to be \'image\'');
        }

        $image = $request->file('image');

        if (!in_array($image->getClientOriginalExtension(), ['jpg', 'png'])) {
            return $this->respondWithMessage('Only jpg or png format images allowed', false);
        }

        $user = $this->authUser();

        $imageName = "{$user->username}.{$image->getClientOriginalExtension()}";

        $imagesStorage = \Storage::disk('public_images');
        if ($imagesStorage->exists($user->image)) {
            $imagesStorage->delete($user->image);
            $this->dLog('deleted old img');
        }

        $path = $imagesStorage->putFileAs("/user/{$user->id}", $image, $imageName);

        if ($user->image !== $path) {
            $this->dLog('new path is different');
            $this->dLog('old path - '.$user->image.', new path - ' . $path);
            $user->image = $path;
            $user->save();
        }

        return $this->respondWithData(['image' => $path]);
    }
}
