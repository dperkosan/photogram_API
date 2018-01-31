<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Comment
 *
 * @property int $id
 * @property string $body
 * @property int $user_id
 * @property int $post_id
 * @property int|null $comment_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\HashtagsLink[] $hashtags
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Like[] $likes
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereUserId($value)
 * @mixin \Eloquent
 */
class Comment extends Model
{
//    protected static function boot()
//    {
//        parent::boot();
//
//        static::addGlobalScope('withStuff', function (Builder $builder) {
//            $builder
//              ->with('user:id,username,image')
//              ->withCount('likes');
//        });
//    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->morphMany('App\Like', 'likable');
    }

    public function hashtags()
    {
        return $this->morphMany('App\HashtagsLink', 'taggable');
    }
}
