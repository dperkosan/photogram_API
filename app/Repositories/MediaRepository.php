<?php

namespace App\Repositories;

use App\Interfaces\MediaRepositoryInterface;
use App\Post;
use Illuminate\Support\Collection;

class MediaRepository implements MediaRepositoryInterface
{
    /**
     * @var string placeholder to be replace with image format name
     */
    protected $placeholder;

    /**
     * @var string name for the original image format
     */
    protected $orig;

    /**
     * @var string name for the original image format
     */
    protected $storage;

//    /**
//     * @var array of paths where different images are stored
//     */
//    protected $imagePaths;
//
//    /**
//     * @var array of paths where different videos are stored
//     */
//    protected $videoPaths;

    public function __construct()
    {
        $this->placeholder = config('boilerplate.image_format_placeholder');
//        $this->imagePaths = config('boilerplate.path.images');
//        $this->videoPaths = config('boilerplate.path.videos');
        $this->orig = 'orig';
    }

    /*
    |--------------------------------------------------------------------------
    | Methods for storing media files
    |--------------------------------------------------------------------------
    */

    /**
     * @return \Illuminate\Filesystem\FilesystemAdapter
     */
    protected function getStorage()
    {
        if (!$this->storage) {
            $this->storage = \Storage::disk('public');
        }

        return $this->storage;
    }

    /**
     * @param \Illuminate\Http\UploadedFile $image
     * @param                               $user
     *
     * @return false|mixed|string
     */
    public function updateUserImage($image, $user)
    {
        $imageNameOrig = "{$user->username}-orig.{$image->getClientOriginalExtension()}";
        $imageName = "{$user->username}-{$this->placeholder}.{$image->getClientOriginalExtension()}";

        $storage = $this->getStorage();
        if ($storage->exists($user->image)) {
            $storage->delete($user->image);
        }

        $path = "/images/user/{$user->id}";

        $imagePath = $storage->putFileAs($path, $image, $imageNameOrig);

        $this->makeThumbs($path, $imageName, 'user');

        $imagePath = str_replace('-orig', "-{$this->placeholder}", $imagePath);

        return $imagePath;
    }

    /**
     * @param \Illuminate\Http\UploadedFile $image
     * @param                               $user
     *
     * @return false|mixed|string
     */
    public function savePostImage($image, $user)
    {
        $imageExtension = $image->getClientOriginalExtension();
        $currentYear = date('Y');
        $namePrefix = date('Ymdhis') . '-' . $user->username;

        $mediaName = "{$namePrefix}-{$this->placeholder}.{$imageExtension}";
        $mediaNameOrig = "{$namePrefix}-orig.{$imageExtension}";

        $path = "images/post/{$user->id}/{$currentYear}";

        $mediaPath = $this->getStorage()->putFileAs($path, $image, $mediaNameOrig);

        $this->makeThumbs($path, $mediaName, 'post');

        // In case there is another "orig" substring in the media path we replace the whole prefix plus "-orig"
        $mediaPath = str_replace("{$namePrefix}-orig", "{$namePrefix}-{$this->placeholder}", $mediaPath);

        return $mediaPath;
    }

    /**
     * @param \Illuminate\Http\UploadedFile $media
     * @param                               $user
     *
     * @return false|mixed|string
     */
    public function savePostVideoOrThumbnail($media, $user)
    {
        $currentYear = date('Y');
        $namePrefix = date('Ymdhis') . '-' . $user->username;

        $mediaName = "{$namePrefix}.{$media->getClientOriginalExtension()}";

        $path = "videos/post/{$user->id}/{$currentYear}";

        return $this->getStorage()->putFileAs($path, $media, $mediaName);
    }

    /**
     * @param string $path where to put the image thumbs
     * @param string $imageName only images can have different sizes ofc
     * @param string $thumbsFor key from file boilerplate.php thumbs array
     */
    public function makeThumbs($path, $imageName, $thumbsFor)
    {
        $absPath = storage_path() . '/app/public/' . $path . '/';
        $thumbs = config('boilerplate.thumbs.' . $thumbsFor);

        $origName = str_replace($this->placeholder, $this->orig, $imageName);

        foreach ($thumbs as $formatName => $formatArray) {
            $thumbName = str_replace($this->placeholder, $formatName, $imageName);
            \File::copy($absPath . $origName, $absPath . $thumbName);

            \Image::make($absPath . $thumbName)->fit($formatArray[0], $formatArray[1], function ($constraint) {
                $constraint->upsize();
            })->save()->destroy();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Methods for deleting media
    |--------------------------------------------------------------------------
    */

    public function deletePostImage($imagePath)
    {
        $thumbs = config('boilerplate.thumbs.post');

        $thumbImages = [];
        foreach ($thumbs as $thumbName => $thumbFormat) {
            $thumbImages[] = str_replace($this->placeholder, $thumbName, $imagePath);
        }

        return $this->getStorage()->delete($thumbImages);
    }

    public function deleteFiles($filePath)
    {
        return $this->getStorage()->delete($filePath);
    }

    /*
    |--------------------------------------------------------------------------
    | Methods for adding thumb paths to image properties
    |--------------------------------------------------------------------------
    */

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
     * @param Collection|Post $posts
     * @param string          $imageAttr
     */
    public function addThumbsToPosts($posts, $imageAttr = 'media')
    {
        $thumbs = config('boilerplate.thumbs.post');

        $postTypeId = Post::TYPE_IMAGE;

        if ($posts instanceof Collection) {
            foreach ($posts as $post) {
                $this->addThumbsToOnePost($post, $thumbs, $imageAttr, $postTypeId);
            }
        } else {
            $this->addThumbsToOnePost($posts, $thumbs, $imageAttr, $postTypeId);
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
     * @param Collection|Post $users
     * @param string          $imageAttr
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
            $thumbImages = $this->generateThumbsFromString($object->$imageAttr, $thumbs);
            $object->$imageAttr = $thumbImages;
        }
    }

    /**
     * @param string $imagePathString string with $this->placeholder string somewhere in it
     * @param array  $thumbs
     *
     * @return array
     */
    public function generateThumbsFromString($imagePathString, $thumbs)
    {
        $thumbImages = [];
        foreach ($thumbs as $thumbName => $thumbFormat) {
            $thumbImages[$thumbName] = str_replace($this->placeholder, $thumbName, $imagePathString);
        }

        $thumbImages['placeholder'] = $imagePathString;
        $thumbImages[$this->orig] = str_replace($this->placeholder, $this->orig, $imagePathString);

        return $thumbImages;
    }
}