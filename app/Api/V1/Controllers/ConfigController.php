<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Documentation\Generator;

class ConfigController extends ApiController
{
    public function index()
    {
        $data = $this->baseData();

        $data['image_formats'] = config('boilerplate.thumbs');
        $data['default_user_images'] = config('boilerplate.default_user_images');

        $data['documentation'] = Generator::getInstance()->getData();

        return $this->respondWithData($data);
    }

    protected function baseData()
    {
        return [
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
        ];
    }
}