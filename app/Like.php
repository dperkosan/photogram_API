<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    // likable_type
    const LIKABLE_POST = 1;
    const LIKABLE_COMMENT = 2;

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
