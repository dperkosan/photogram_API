<?php

namespace App\Repositories;

use Hash;
use App\User;
use App\Interfaces\UserRepositoryInterface;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use App\Notifications\ConfirmEmail as ConfirmEmailNotification;

class UserRepository implements UserRepositoryInterface
{

    /**
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getAll()
    {
        return $this->user->all();
    }

    public function getById($userId)
    {
        return $this->user->find($userId);
    }

    public function store($data)
    {
        $user = $this->fillUserObject($this->user, $data);
        if($user->save()) return $user;
        return false;
    }

    /**
     * Fill User Object
     *
     * @param $object
     * @param array $data
     */
    private function fillUserObject($object, array $data)
    {
        if(isset($data['email']))
        {
            $object->email = $data['email'];
        }

        if(isset($data['password']))
        {
            $object->password = bcrypt($data['password']);
        }

        if(isset($data['username']))
        {
            $object->username = $data['username'];
        }

        if(isset($data['name']))
        {
            $object->name = $data['name'];
        }

        if(isset($data['gender']))
        {
            $object->gender = $data['gender'];
        }

        if(isset($data['phone']))
        {
            $object->phone = $data['phone'];
        }

        if(isset($data['about']))
        {
            $object->about = $data['about'];
        }

        $object->active = 0;

        return $object;
    }

    /**
     * Automatically creates hash for the user password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->user->attributes['password'] = Hash::make($value);
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
}