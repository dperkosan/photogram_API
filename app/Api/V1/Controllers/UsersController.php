<?php
namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\UserRequest;
use App\User;
use Illuminate\Support\Facades\Auth;

class UsersController extends ApiController
{
    public function updateUser(UserRequest $request)
    {
        $userData = $request->only([
          'name', 'gender_id', 'phone', 'about'
        ]);

        $user = Auth::user();

        if ($user->update($userData)) {
            return $this->setStatusCode(204)->respond();
        }
        return $this->respondInternalError();
    }

    public function checkUsername($username)
    {
        $exists = false;
        if (User::where('username', '=', $username)->exists()) {
            $exists = true;
        }
        return $this->respondWithData(['exists' => $exists]);
    }
}
