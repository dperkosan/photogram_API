<?php

namespace App\Api\V1\Traits;

use Intervention\Image\Facades\Image;

trait ThumbsTrait
{

    /**
     * @param        $path
     * @param        $media
     * @param        $mediaName
     * @param string $thumbsFor
     */
    public function makeThumbs($path, $media, $mediaName, $thumbsFor = 'post')
    {
        $user = $this->authUser();

        $absPath = storage_path() . '/app/public/' . $path . '/';
        $thumbs = config('boilerplate.thumbs.' . $thumbsFor);

        $mediaExtension = $media->getClientOriginalExtension();

        foreach ($thumbs as $thumb_name => $thumb_format) {
            $mediaNameS = date('Ymdhis') . "-{$user->username}-{$thumb_name}.{$mediaExtension}";
            \Illuminate\Support\Facades\File::copy($absPath . $mediaName, $absPath . $mediaNameS);

            Image::make($absPath . $mediaNameS)->fit($thumb_format[0], $thumb_format[1], function ($constraint) {
                $constraint->upsize();
            })->save()->destroy();
        }
    }

}