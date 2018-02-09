<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Post
 *
 * @property int                                                               $id
 * @property int                                                               $user_id
 * @property int                                                               $type_id
 * @property string                                                            $media
 * @property string|null                                                       $thumbnail
 * @property string|null                                                       $description
 * @property \Carbon\Carbon                                                    $created_at
 * @property \Carbon\Carbon|null                                               $updated_at
 * @property \Carbon\Carbon|null                                               $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[]      $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\HashtagsLink[] $hashtags
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Like[]         $likes
 * @property-read \App\User                                                    $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Post onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereMedia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Post withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Post withoutTrashed()
 * @mixin \Eloquent
 */
class Post extends Model
{
    use SoftDeletes;

    const TYPE_IMAGE = 1;
    const TYPE_VIDEO = 2;

    protected $dates = ['deleted_at'];

    protected $fillable = ['user_id', 'type_id', 'media', 'thumbnail', 'description'];

    protected $hidden = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class)->withoutGlobalScopes();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
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
