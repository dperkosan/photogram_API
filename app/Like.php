<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Like extends Model
{
    use SoftDeletes;

    // likable_type
    const LIKABLE_POST = 1;
    const LIKABLE_COMMENT = 2;

    protected $dates = ['deleted_at'];

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
