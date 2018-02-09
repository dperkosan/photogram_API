<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Follower
 *
 * @property int $id
 * @property int $follower_id
 * @property int $followed_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Follower whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Follower whereFollowedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Follower whereFollowerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Follower whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Follower whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Follower extends Model
{
    protected $fillable = ['followed_id', 'follower_id'];
}
