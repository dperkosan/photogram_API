<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\HashtagsLink
 *
 * @property int $id
 * @property int $hashtag_id
 * @property int $taggable_id
 * @property int $taggable_type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $taggable
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HashtagsLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HashtagsLink whereHashtagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HashtagsLink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HashtagsLink whereTaggableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HashtagsLink whereTaggableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HashtagsLink whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HashtagsLink extends Model
{

    const TAGGABLE_POST = 1;
    const TAGGABLE_COMMENT = 2;

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
