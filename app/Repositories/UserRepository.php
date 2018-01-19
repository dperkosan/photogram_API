<?php

namespace App\Repositories;

use App\User;
use App\Interfaces\UserRepositoryInterface;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use App\Notifications\ConfirmEmail as ConfirmEmailNotification;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var \App\User
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

    public function getByEmail($email)
    {
        return $this->user->where('email', $email)->firstOrFail();
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
        $attributesToFill = ['email', 'password', 'username', 'name', 'gender', 'phone', 'about'];

        foreach ($attributesToFill as $attribute)
        {
            if (isset($data[$attribute]))
            {
                $object->$attribute = $data[$attribute];
            }
        }

        // Only setting the password is unique because of the bcrypt
        if(isset($data['password']))
        {
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
}