<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('withStuff', function (Builder $builder) {
            $builder->with('user:id,username');
        });
    }

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
