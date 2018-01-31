<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\PostDataRequest;
use App\Api\V1\Requests\PostRequest;
use App\HashtagsLink;
use App\Interfaces\HashtagRepositoryInterface;
use App\Interfaces\PostRepositoryInterface;
use App\Post;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

class PostsController extends ApiController
{
    /**
     * @var \App\Repositories\PostRepository
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
     * @param PostDataRequest $request
     *
     * @return mixed
     */
    public function getPosts(PostDataRequest $request)
    {
        $posts = $this->posts->getPosts($request->amount, $request->page);

        return $this->respondWithData($posts);
    }

    public function newsFeed(PostDataRequest $request)
    {
        $posts = $this->posts->newsFeed($this->authUser()->id, $request->amount, $request->page);

        return $this->respondWithData($posts);
    }

    public function index(PostDataRequest $request)
    {
        $userId = null;
        if ($request->username) {
            $userId = \App\User::where('username', $request->username)->first()->id;
        } else if ($request->user_id) {
            $userId = $request->user_id;
        }

        $posts = $this->posts->getPosts($request->amount, $request->page, $userId);

        return $this->respondWithData($posts);
    }

    public function show(Post $post)
    {
        return $this->respondWithData($post);
    }

    public function store(PostRequest $request, HashtagRepositoryInterface $hashtags)
    {
        $mediaType = $request->image ? 'image' : 'video';
        $isImage = $mediaType === 'image';

        $media = $request->file($mediaType);
        $mediaExtension = $media->getClientOriginalExtension();

        $user = $this->authUser();
        $userId = $user->id;
        $currentYear = date('Y');

        $mediaName = date('Ymdhis') . "-{$user->username}.{$mediaExtension}";
        $folder = $mediaType . 's';
        $path = "{$folder}/post/{$userId}/{$currentYear}";
        $storage = \Storage::disk('public');

        $mediaPath = $storage->putFileAs($path, $media, $mediaName);

        $thumbnailPath = null;
        if (!$isImage) {
            $thumbnail = $request->file('thumbnail');
            $user = $this->authUser();
            $thumbnailName = date('Ymdhis') . "-{$user->username}.{$thumbnail->getClientOriginalExtension()}";
            $path = "videos/post/{$userId}/{$currentYear}";

            $thumbnailPath = $storage->putFileAs($path, $thumbnail, $thumbnailName);
        }

        $post = Post::create([
          'user_id'     => $user->id,
          'type_id'     => $isImage ? Post::TYPE_IMAGE : Post::TYPE_VIDEO,
          'media'       => $mediaPath,
          'thumbnail'   => $thumbnailPath,
          'description' => $request->description,
        ]);

        $hashtags->saveHashtags($post->id, HashtagsLink::TAGGABLE_POST, $post->description);

        return $this->respondCreated();
    }

    public function update(Request $request, Post $post, HashtagRepositoryInterface $hashtags)
    {
        if (!$this->belongsToAuthUser($post)) {
            return $this->respondForbidden('This post is not yours!');
        }

        if (isset($request->description)) {
            $post->description = $request->description;
            $hashtags->saveHashtags($post->id, HashtagsLink::TAGGABLE_POST, $post->description);
        }

        if (isset($request->thumbnail)) {
            $storage = \Storage::disk('public');
            if ($storage->exists($post->thumbnail)) {
                $storage->delete($post->thumbnail);
            }
            $post->thumbnail = $request->thumbnail;
        }

        $post->save();

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