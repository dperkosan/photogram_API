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

        $origName = str_replace('[~FORMAT~]', 'orig', $mediaName);

        foreach ($thumbs as $formatName => $formatArray) {
            $thumbName = str_replace('[~FORMAT~]', $formatName, $mediaName);
            \File::copy($absPath . $origName, $absPath . $thumbName);

            \Image::make($absPath . $thumbName)->fit($formatArray[0], $formatArray[1], function ($constraint) {
                $constraint->upsize();
            })->save()->destroy();
        }
    }

}