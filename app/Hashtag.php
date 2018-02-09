<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Hashtag
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hashtag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hashtag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hashtag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hashtag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Hashtag extends Model
{
    protected $fillable = ['name'];
}
