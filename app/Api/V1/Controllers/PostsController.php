<?php

namespace App\Api\V1\Controllers;

use App\HashtagsLink;
use App\Interfaces\HashtagRepositoryInterface;
use App\Interfaces\PostRepositoryInterface;
use App\Post;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

class PostsController extends ApiController
{
    /**
     * @var \App\Interfaces\FollowerRepositoryInterface
     */
    private $posts;
    private $jwtAuth;

    public function __construct(JWTAuth $jwtAuth, PostRepositoryInterface $posts)
    {
        $this->jwtAuth = $jwtAuth;
        $this->posts = $posts;
    }

    /**
     * Get x number of latest posts
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function getPosts(Request $request)
    {
        if (!$request->amount || !$request->page) {
            return $this->respondWrongArgs('Query parameters \'amount\' and \'page\' are required.');
        }
        $posts = $this->posts->getPosts($request->amount, $request->page);

        return $this->respondWithData($posts);
    }

    public function index(Request $request)
    {
        $this->validate($request, [
          'amount' => 'required|max:100',
          'page'   => 'required|max:50',
        ]);

        $posts = $this->posts->getPosts($request->amount, $request->page);

        return $this->respondWithData($posts);
    }

    public function show(Post $post)
    {
        return $this->respondWithData($post);
    }

    public function store(Request $request, HashtagRepositoryInterface $hashtags)
    {
        if (!$request->hasFile('media')) {
            return $this->respondWrongArgs('Image or video with the param name \'media\' is required');
        }

        $media = $request->file('media');
        $mediaExtension = $media->getClientOriginalExtension();

        // TODO: check for mime type instead of extension

        if (in_array($mediaExtension, \Config::get('boilerplate.allowed.formats.image'))) {
            $isImage = true;
        } else if (in_array($mediaExtension, \Config::get('boilerplate.allowed.formats.video'))) {
            $isImage = false;
        } else {
            return $this->respondWrongArgs('Format ' . $mediaExtension . ' is not allowed.');
        }

        $user = $this->authUser();
        $mediaName = date('Ymdhis') . "-{$user->username}.{$mediaExtension}";
        $folder = $isImage ? 'images' : 'videos';
        $path = "{$folder}/post/{$user->id}/" . date('Ymd');

        $path = \Storage::disk('public')->putFileAs($path, $media, $mediaName);

        $thumbnail = null;
        if (!$isImage) {
            $thumbnail = $this->processThumbnail($request);
        }

        $post = Post::make([
          'user_id'     => $user->id,
          'type_id'     => $isImage ? Post::TYPE_IMAGE : Post::TYPE_VIDEO,
          'media'       => $path,
          'thumbnail'   => $thumbnail,
          'description' => $request->description,
        ]);

        if (!$post->save()) {
            return $this->respondInternalError('Falied to save post');
        }

        $hashtags->saveHashtags($post->id, HashtagsLink::TAGGABLE_POST, $post->description);

        return $this->respondCreated();
    }

    private function processThumbnail(Request $request)
    {
        $this->validate($request, [
          'thumbnail' => 'required|image',
        ]);

        $thumbnail = $request->file('thumbnail');
        $user = $this->authUser();
        $thumbnailName = date('Ymdhis') . "-{$user->username}.{$thumbnail->getClientOriginalExtension()}";
        $storage = \Storage::disk('public');

        return $storage->putFileAs("videos/post/{$user->id}/" . date('Ymd'), $thumbnail, $thumbnailName);
    }

    public function update(Request $request, Post $post, HashtagRepositoryInterface $hashtags)
    {
        if (!isset($request->description)) {
            return $this->respondWrongArgs('Only description can and must be sent in the request body');
        }

        if (!$this->belongsToAuthUser($post)) {
            return $this->respondForbidden('This post is not yours!');
        }

        $post->description = $request->description;

        if (!$post->save()) {
            $this->respondInternalError('Falied to save post');
        }

        $hashtags->saveHashtags($post->id, HashtagsLink::TAGGABLE_POST, $post->description);

        return $this->respondSuccess();
    }

    public function destroy(Post $post)
    {
        if (!$this->belongsToAuthUser($post)) {
            return $this->respondForbidden('This post is not yours!');
        }

        if (!$post->delete()) {
            return $this->respondInternalError('Failed to delete post');
        }

        return $this->respondSuccess();
    }
}