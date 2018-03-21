<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    const TYPE_IMAGE = 1;
    const TYPE_VIDEO = 2;

    protected $fillable = ['user_id', 'type_id', 'media', 'thumbnail', 'description'];

    protected $hidden = ['deleted_at'];

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
        return $this->morphMany('App\Like', 'likable');
    }

    public function hashtags()
    {
        return $this->morphMany('App\HashtagsLink', 'taggable');
    }
}
