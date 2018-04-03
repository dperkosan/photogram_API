<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Elasticquent\ElasticquentTrait;

class Hashtag extends Model
{
    use ElasticquentTrait;

    protected $fillable = ['name'];

    public function hashtagsLink()
    {
        return $this->hasMany(HashtagsLink::class);
    }

    public function getWithHashAttribute()
    {
        return '#' . $this->name;
    }
}
