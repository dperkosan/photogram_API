<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    protected $fillable = ['name'];

    public function hashtagsLink()
    {
        return $this->hasMany(HashtagsLink::class);
    }
}
