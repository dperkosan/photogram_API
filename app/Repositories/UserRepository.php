<?php

namespace App\Repositories;

use App\User;
use Illuminate\Database\Eloquent\Collection;
use Tymon\JWTAuth\JWTAuth;
use App\Interfaces\UserRepositoryInterface;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use App\Notifications\ConfirmEmail as ConfirmEmailNotification;
use App\Notifications\Followed as FollowedNotification;

class UserRepository extends Repository implements UserRepositoryInterface
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

    public function addThumbs($users, $imageAttr = 'image')
    {
        $thumbs = config('boilerplate.thumbs.user');

        if ($users instanceof Collection) {
            foreach ($users as $user) {
                $this->addThumbsToOneUser($user, $thumbs);
            }
        } else {
            $this->addThumbsToOneUser($users, $thumbs, $imageAttr);
        }
    }

    protected function addThumbsToOneUser($user, $thumbs, $imageAttr = 'image')
    {
        $user->thumbs = null;

        if (!isset($user->$imageAttr) || empty($user->$imageAttr)) {
            $user->thumbs = config('boilerplate.default_user_images');

        } else if (strpos($user->$imageAttr, '[~FORMAT~]') !== false) {

            $arr_thumbs = [];
            foreach ($thumbs as $thumb_name => $thumb_format) {
                $arr_thumbs[$thumb_name] = str_replace('[~FORMAT~]', $thumb_name, $user->$imageAttr);
            }
            $arr_thumbs['orig'] = str_replace('[~FORMAT~]', 'orig', $user->$imageAttr);
            $user->thumbs = $arr_thumbs;
        }
    }

    public function addCounts($user)
    {
        $user->posts_count = \DB::table('posts')->where('user_id', $user->id)->count();
        $user->followers_count = \DB::table('followers')->where('followed_id', $user->id)->count();
        $user->following_count = \DB::table('followers')->where('follower_id', $user->id)->count();

        return $user;
    }

    public function addIsFollowed($users, $authUserId)
    {
        if ($users instanceof Collection) {
            foreach ($users as $user) {
                $this->addIsFollowedToOneUser($user, $authUserId);
            }
        } else {
            $this->addIsFollowedToOneUser($users, $authUserId);
        }
    }

    private function addIsFollowedToOneUser($user, $authUserId)
    {
        if (!isset($user->id)) {
            return;
        }
        $user->auth_follow = \DB::table('followers')->where([
          'follower_id' => $authUserId,
          'followed_id' => $user->id,
        ])->exists();
    }

    public function usersFromLikes($likableId, $likableType, $amount, $page)
    {
        $offset = $this->calcOffset($amount, $page);

        return $this->user
          ->select(['users.id', 'users.username', 'users.image'])
          ->join('likes', 'users.id', '=', 'likes.user_id')
          ->where([
            'likable_id' => $likableId,
            'likable_type' => $likableType,
          ])
          ->offset($offset)
          ->limit($amount)
          ->get();
    }

    public function usersMutualFollowers(array $userIds, $amount, $page)
    {
        $offset = $this->calcOffset($amount, $page);

        return $this->user
          ->select(['users.id', 'users.username', 'users.image'])
          ->join('followers', 'users.id', '=', 'followers.follower_id')
          ->whereIn('followers.follower_id', $userIds)
          ->groupBy('followers.follower_id')
          ->havingRaw('COUNT(`followers`.`follower_id`) >= ' . count($userIds))
          ->offset($offset)
          ->limit($amount)
          ->get();
    }

    protected function fullQuery()
    {
        return $this->user->withCount(['posts', 'followers', 'following']);
    }

    /**
     * @return \App\User
     */
    public function findWhere($column, $operator = null, $value = null, $boolean = 'and')
    {
        return $this->fullQuery()->where(...func_get_args())->first();
    }

    /**
     * @return \App\User
     */
    public function findById($id)
    {
        return $this->findWhere('id', $id);
    }

    /**
     * @return \App\User
     */
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