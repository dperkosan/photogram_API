<?php

namespace App\Repositories;

use App\Hashtag;
use App\HashtagsLink;
use App\Interfaces\HashtagRepositoryInterface;

class HashtagRepository implements HashtagRepositoryInterface
{
    /**
     * @var \App\Hashtag
     */
    private $hashtag;

    public function __construct(Hashtag $hashtag)
    {
        $this->hashtag = $hashtag;
    }

    /**
     * @param integer $taggableId
     * @param integer $taggableType
     * @param string  $text
     */
    public function saveHashtags($taggableId, $taggableType, $text)
    {
        // get hashtags from text
        preg_match_all('/#(\w+)/', $text, $matches);

        $hashtags = array_unique($matches[1]);

        // delete old hashtag links
        HashtagsLink::where([
          'taggable_id' => $taggableId,
          'taggable_type' => $taggableType,
        ])->delete();

        if (count($hashtags) <= 0) {
            return;
        }

        // insert hashtags into database if not already in
        $hashtagLinkData = [];
        foreach ($hashtags as $hashtag) {
            $hashtagInstance = $this->hashtag->firstOrCreate([
              'name' => $hashtag,
            ]);

            // While inserting hashtags, fill the data to insert later
            $hashtagLinkData[] = [
              'hashtag_id' => $hashtagInstance->id,
              'taggable_id' => $taggableId,
              'taggable_type' => $taggableType,
            ];
        }

        // insert new hashtag links
        \DB::table('hashtags_link')->insert($hashtagLinkData);
    }

}