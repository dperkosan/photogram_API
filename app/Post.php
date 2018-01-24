<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('withStuff', function (Builder $builder) {
            $builder
              ->with('user:id,username,image')
              ->withCount('likes');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->morphMany('App\Like', 'likable');
    }
}
