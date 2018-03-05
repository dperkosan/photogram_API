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
              'male' => 1,
              'female' => 2,
              'other' => 3,
            ],
          ],

          'post' => [
            'types' => [
              'picture' => 1,
              'video' => 2,
            ],
          ],

          'like' => [
            'likable_types' => [
              'post' => 1,
              'comment' => 2,
            ],
          ],

          'hashtag' => [
            'taggable_types' => [
              'post' => 1,
              'comment' => 2,
            ],
          ],
        ];
    }
}