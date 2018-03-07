<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HashtagsLink extends Model
{
    use SoftDeletes;

    const TAGGABLE_POST = 1;
    const TAGGABLE_COMMENT = 2;

    protected $dates = ['deleted_at'];

    protected $table = 'hashtags_link';

    protected $fillable = ['hashtag_id', 'taggable_id', 'taggable_type'];

    /**
     * Get all of the owning commentable models.
     */
    public function taggable()
    {
        return $this->morphTo();
    }
    
    public function hashtag()
    {

    }

}
