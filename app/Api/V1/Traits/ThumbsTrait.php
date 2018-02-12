<?php

namespace App\Api\V1\Traits;

trait ThumbsTrait
{

    /**
     * @param        $path
     * @param        $mediaName
     * @param string $thumbsFor
     */
    public function makeThumbs($path, $mediaName, $thumbsFor = 'post')
    {
        $absPath = storage_path() . '/app/public/' . $path . '/';
        $thumbs = config('boilerplate.thumbs.' . $thumbsFor);

        foreach ($thumbs as $formatName => $formatArray) {
//            $mediaNameS = date('Ymdhis') . "-{$user->username}-{$thumb_name}.{$mediaExtension}";
            $thumbName = str_replace('[~FORMAT~]', $formatName, $mediaName);
            $origName = str_replace('[~FORMAT~]', 'orig', $mediaName);
            \File::copy($absPath . $origName, $absPath . $thumbName);

            \Image::make($absPath . $thumbName)->fit($formatArray[0], $formatArray[1], function ($constraint) {
                $constraint->upsize();
            })->save()->destroy();
        }
    }

}