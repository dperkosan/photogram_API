<?php

namespace App\Interfaces;


interface HashtagRepositoryInterface
{
    /**
     * @param integer $taggableId
     * @param integer $taggableType
     * @param string  $text
     */
    public function saveHashtags($taggableId, $taggableType, $text);
}