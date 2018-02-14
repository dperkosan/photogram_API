<?php

namespace App\Interfaces;


interface ImageRepositoryInterface
{
    public function addThumbs($objects, array $thumbs, $imageAttr);
    public function addThumbsToUsers($objects, $imageAttr = 'image');
    public function addThumbsToPosts($objects, $imageAttr = 'media');
}