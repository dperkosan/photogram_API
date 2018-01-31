<?php

namespace App\Api\V1\Controllers;

class ConfigController extends ApiController
{
    public function index()
    {
        return $this->respondWithData([
          'user' => [
            'genders' => [
              1 => 'male',
              2 => 'female',
              3 => 'other',
            ],
          ],

          'post' => [
            'types' => [
              1 => 'picture',
              2 => 'video',
            ],
          ],

          'like' => [
            'likable_types' => [
              1 => 'post',
              2 => 'comment',
            ],
          ],

          'hashtag' => [
            'taggable_types' => [
              1 => 'post',
              2 => 'comment',
            ],
          ],
        ]);
    }
}