<?php

namespace App\Interfaces;


interface MediaRepositoryInterface
{
    public function addThumbs($objects, array $thumbs, $imageAttr);
    public function addThumbsToUsers($objects, $imageAttr = 'image');
    public function addThumbsToPosts($objects, $imageAttr = 'media');

    public function savePostImage($image, $User);
    public function savePostVideoOrThumbnail($media, $user);
    public function makeThumbs($path, $mediaName, $thumbsFor);
    public function updateUserImage($image, $user);

    public function deletePostImage($imagePath);
    public function deleteFiles($filePaths);
}