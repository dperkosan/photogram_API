<?php

namespace App\Repositories;

use App\User;
use Tymon\JWTAuth\JWTAuth;
use App\Interfaces\UserRepositoryInterface;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use App\Notifications\ConfirmEmail as ConfirmEmailNotification;
use App\Notifications\Followed as FollowedNotification;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var \App\User
     */
    protected $user;
    protected $JWTAuth;

    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    const GENDER_OTHER = 3;

    public function __construct(User $user, JWTAuth $JWTAuth)
    {
        $this->user = $user;
        $this->JWTAuth = $JWTAuth;
    }

    public function getAuthUser()
    {
        $authUserId = $this->JWTAuth->getPayload()->get('sub');
        return;
    }

    protected function fullQuery()
    {
        return $this->user;
    }
    
    public function findWhere($column, $operator = null, $value = null, $boolean = 'and')
    {
        return $this->fullQuery()->where(...func_get_args())->first();
    }

    public function findById($id)
    {
        return $this->findWhere('id', $id);
    }
    
    public function findByEmail($email)
    {
        return $this->findWhere('email', $email);
    }

    public function existsWhere($column, $operator = null, $value = null, $boolean = 'and')
    {
        return $this->fullQuery()->where(...func_get_args())->exists();
    }

    public function emailExists($email)
    {
        return $this->existsWhere('email', $email);
    }

    public function usernameExists($username)
    {
        return $this->existsWhere('username', $username);
    }

    public function store($data)
    {
        $user = $this->fillUserObject($data);
        if($user->save()) return $user;
        return false;
    }

    /**
     * Fill User Object
     *
     * @param array $data
     * @param       $object
     *
     * @return mixed
     */
    private function fillUserObject(array $data, $object = null)
    {
        $object = ($object) ? $object : $this->user;

        // In order not to write the same if condition 7 times
        $attributesToFill = ['email', 'password', 'username', 'name', 'gender_id', 'phone', 'about'];

        foreach ($attributesToFill as $attribute) {
            if (isset($data[$attribute])) {
                $object->$attribute = $data[$attribute];
            }
        }

        // Only setting the password is unique because of the bcrypt
        if(isset($data['password'])) {
            $object->password = bcrypt($data['password']);
        }

        $object->active = 0;

        return $object;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->user->notify(new ResetPasswordNotification($token));
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendConfirmEmailNotification($token)
    {
        $this->user->notify(new ConfirmEmailNotification($token));
    }

    /**
     * Send notification about new follower to followed user.
     *
     * @param  string  $token
     * @return void
     */
    public function sendNotificationToFollowed($token)
    {
        $this->user->notify(new FollowedNotification($token));
    }
}