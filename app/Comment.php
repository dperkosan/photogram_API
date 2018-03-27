<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['body', 'post_id', 'comment_id', 'user_id', 'reply_user_id'];

    public function user()
    {
        return $this->belongsTo(User::class)->withoutGlobalScopes();
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
