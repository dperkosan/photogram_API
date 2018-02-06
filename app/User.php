<?php
namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\User
 *
 * @property int $id
 * @property string $username
 * @property string|null $name
 * @property string $email
 * @property string $password
 * @property int|null $gender_id
 * @property string|null $phone
 * @property string|null $about
 * @property string|null $image
 * @property int $type_id
 * @property int $active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Follower[] $followers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Follower[] $following
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Post[] $posts
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAbout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereGenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUsername($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    const GENDER_OTHER = 3;

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'name', 'email', 'password', 'gender_id', 'phone', 'about', 'image'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'active', 'type_id', 'created_at', 'updated_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('counts', function (Builder $builder) {
            $builder->withCount(['posts', 'followers', 'following']);
        });
    }

    /**
     * Users that follow me
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function followers()
    {
        return $this->hasMany(Follower::class, 'followed_id');
    }

    /**
     * Users that I follow
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function following()
    {
        return $this->hasMany(Follower::class, 'follower_id');
    }
    
    /**
     * My posts
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }
}
