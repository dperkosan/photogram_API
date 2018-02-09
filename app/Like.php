<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Like
 *
 * @property int $id
 * @property int $user_id
 * @property int $likable_id
 * @property int $likable_type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $likable
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Like whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Like whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Like whereLikableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Like whereLikableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Like whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Like whereUserId($value)
 * @mixin \Eloquent
 */
class Like extends Model
{
    // likable_type
    const LIKABLE_POST = 1;
    const LIKABLE_COMMENT = 2;

    protected $fillable = ['user_id', 'likable_id', 'likable_type'];

    protected $hidden = ['created_at','updated_at'];

    /**
     * Get all of the owning commentable models.
     */
    public function likable()
    {
        return $this->morphTo();
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
