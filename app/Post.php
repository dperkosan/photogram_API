<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Elasticquent\ElasticquentTrait;

class Post extends Model
{
    use ElasticquentTrait;

    const TYPE_IMAGE = 1;
    const TYPE_VIDEO = 2;

    protected $fillable = ['user_id', 'type_id', 'media', 'thumbnail', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class)->withoutGlobalScopes();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likable');
    }

    public function hashtags()
    {
        return $this->morphMany(HashtagsLink::class, 'taggable');
    }


}
