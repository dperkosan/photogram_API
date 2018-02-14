<?php

namespace App\Repositories;

use App\Interfaces\ImageRepositoryInterface;
use Illuminate\Support\Collection;

class ImageRepository implements ImageRepositoryInterface
{
    protected $placeholder = '[~FORMAT~]';

    /**
     * @param mixed  $objects       collection of object or one object
     * @param array  $thumbs        thumbName => thumbFormat array
     * @param string $imageAttr     attribute name that contains img src
     * @param array  $defaultThumbs in case that $imageAttr is empty these are used to fill thumbs directly
     */
    public function addThumbs($objects, array $thumbs, $imageAttr = 'image', $defaultThumbs = null)
    {
        if ($objects instanceof Collection) {
            foreach ($objects as $object) {
                $this->addThumbsToOneObject($object, $thumbs, $imageAttr, $defaultThumbs);
            }
        } else {
            $this->addThumbsToOneObject($objects, $thumbs, $imageAttr, $defaultThumbs);
        }
    }

    /**
     * @param Collection|\App\Post $posts
     * @param string               $imageAttr
     */
    public function addThumbsToPosts($posts, $imageAttr = 'media')
    {
        $thumbs = config('boilerplate.thumbs.post');

        $postTypeId = \App\Post::TYPE_IMAGE;

        if ($posts instanceof Collection) {
            foreach ($posts as $post) {
                $this->addThumbsToOnePost($post, $thumbs, $imageAttr, $postTypeId);
            }
        } else {
            $this->addThumbsToOneObject($posts, $thumbs, $imageAttr, $postTypeId);
        }
    }

    protected function addThumbsToOnePost($post, $thumbs, $imageAttr, $postTypeId)
    {
        if ($post->type_id === $postTypeId) {
            $this->addThumbsToOneObject($post, $thumbs, $imageAttr);
        }
        if (isset($post->comments_count) && $post->comments_count > 0) {
            $this->addThumbsToUsers($post->comments, 'user_image');
        }
    }

    /**
     * @param Collection|\App\Post $users
     * @param string               $imageAttr
     */
    public function addThumbsToUsers($users, $imageAttr = 'image')
    {
        $thumbs = config('boilerplate.thumbs.user');

        $defaultThumbs = config('boilerplate.default_user_images');

        $this->addThumbs($users, $thumbs, $imageAttr, $defaultThumbs);
    }

    /**
     * @param mixed  $object
     * @param array  $thumbs        thumbName => thumbFormat array
     * @param string $imageAttr     attribute name that contains img src
     * @param null   $defaultThumbs in case that $imageAttr is empty these are used to fill thumbs directly
     */
    protected function addThumbsToOneObject($object, $thumbs, $imageAttr = 'image', $defaultThumbs = null)
    {
        // If the attribute that contains image src is empty then use default thumbs
        if (!isset($object->$imageAttr) || empty($object->$imageAttr)) {
            $object->$imageAttr = $defaultThumbs;

        } else if (strpos($object->$imageAttr, $this->placeholder) !== false) {

            $thumbImages = [];
            foreach ($thumbs as $thumbName => $thumbFormat) {
                $thumbImages[$thumbName] = str_replace($this->placeholder, $thumbName, $object->$imageAttr);
            }

            $thumbImages['placeholder'] = $object->$imageAttr;
            $thumbImages['orig'] = str_replace($this->placeholder, 'orig', $object->$imageAttr);

            $object->$imageAttr = $thumbImages;
        }
    }

}