<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\PostPaginationRequest;
use App\Api\V1\Requests\PostRequest;
use App\HashtagsLink;
use App\Interfaces\HashtagRepositoryInterface;
use App\Interfaces\MediaRepositoryInterface;
use App\Interfaces\PostRepositoryInterface;
use App\Post;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use App\Api\V1\Traits\ThumbsTrait;

class PostsController extends ApiController
{
    use ThumbsTrait;

    /**
     * @var \App\Repositories\PostRepository
     */
    private $posts;
    private $jwtAuth;
    private $mediaRepo;

    public function __construct(JWTAuth $jwtAuth, PostRepositoryInterface $posts, MediaRepositoryInterface $mediaRepo)
    {
        $this->jwtAuth = $jwtAuth;
        $this->posts = $posts;
        $this->imageRepository = $mediaRepo;
    }

    public function newsFeed(PostPaginationRequest $request)
    {
        return $this->baseNewsFeed($request);
    }

    private function baseNewsFeed($request)
    {
        $userId = $this->authUser()->id;

        $posts = $this->posts->newsFeed($userId, $request->amount, $request->page);

        $this->posts->addAuthLike($posts, $userId);

        $this->imageRepository->addThumbsToPosts($posts);
        $this->imageRepository->addThumbsToUsers($posts, 'user_image');

        return $this->respondWithData($posts);
    }

    public function index(PostPaginationRequest $request)
    {
        if (isset($request->news_feed)) {
            return $this->baseNewsFeed($request);
        }

        $userId = null;
        if ($request->username) {
            $userId = \App\User::where('username', $request->username)->first()->id;
        } else if ($request->user_id) {
            $userId = $request->user_id;
        }

        $posts = $this->posts->getPosts($request->amount, $request->page, $userId);

        $authUser = $this->authUser();
        if ($authUser) {
            $this->posts->addAuthLike($posts, $authUser->id);
        }
        $this->imageRepository->addThumbsToPosts($posts);
        $this->imageRepository->addThumbsToUsers($posts, 'user_image');

        return $this->respondWithData($posts);
    }

    public function show($post)
    {
        $posts = $this->posts->getPost($post);

        $authUser = $this->authUser();
        if ($authUser) {
            $this->posts->addAuthLike($posts, $authUser->id);
        }
        $this->imageRepository->addThumbsToPosts($posts);
        $this->imageRepository->addThumbsToUsers($posts, 'user_image');

        return $this->respondWithData($posts->first());
    }

    public function store(PostRequest $request, MediaRepositoryInterface $mediaRepo, HashtagRepositoryInterface $hashtags)
    {
        $mediaType = $request->image ? 'image' : 'video';
        $isImage = $mediaType === 'image';
        $media = $request->file($mediaType);
        $user = $this->authUser();

        if ($isImage) {
            $mediaPath = $mediaRepo->savePostImage($media, $user);
        } else {
            $mediaPath = $mediaRepo->savePostVideo($media, $user);
        }

        $thumbnailPath = null;
        if (!$isImage) {
            $thumbnailPath = $mediaRepo->savePostThumbnail($request->file('thumbnail'), $user);
        }

        $postData = [
            'user_id'     => $user->id,
            'type_id'     => $isImage ? Post::TYPE_IMAGE : Post::TYPE_VIDEO,
            'media'       => $mediaPath,
            'thumbnail'   => $thumbnailPath,
        ];
        if (!empty($request->description)) {
            $postData['description'] = $request->description;
        }

        $post = Post::create($postData);

        $hashtags->saveHashtags($post->id, HashtagsLink::TAGGABLE_POST, $post->description);

        return $this->setStatusCode(201)->respondWithData($post);

    }

    public function storeOld(PostRequest $request, MediaRepositoryInterface $mediaRepo, HashtagRepositoryInterface $hashtags)
    {
        $mediaType = $request->image ? 'image' : 'video';
        $isImage = $mediaType === 'image';

        $media = $request->file($mediaType);
        $mediaExtension = $media->getClientOriginalExtension();

        $user = $this->authUser();
        $userId = $user->id;
        $currentYear = date('Y');

        $namePrefix = date('Ymdhis') . '-' . $user->username;

        $mediaName = "{$namePrefix}-[~FORMAT~].{$mediaExtension}";
        $mediaNameOrig = "{$namePrefix}-orig.{$mediaExtension}";

        $folder = $mediaType . 's';
        $path = "{$folder}/post/{$userId}/{$currentYear}";
        $storage = \Storage::disk('public');

        $mediaPath = $storage->putFileAs($path, $media, $mediaNameOrig);

        // make some thumbs
        if ($mediaType === 'image') {
            $mediaRepo->makeThumbs($path, $mediaName, 'post');
        }

        $mediaPath = str_replace("{$namePrefix}-orig", "{$namePrefix}-[~FORMAT~]", $mediaPath);

        $thumbnailPath = null;
        if (!$isImage) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = $namePrefix . $thumbnail->getClientOriginalExtension();
            $path = "videos/post/{$userId}/{$currentYear}";

            $thumbnailPath = $storage->putFileAs($path, $thumbnail, $thumbnailName);
        }

        $postData = [
            'user_id'     => $user->id,
            'type_id'     => $isImage ? Post::TYPE_IMAGE : Post::TYPE_VIDEO,
            'media'       => $mediaPath,
            'thumbnail'   => $thumbnailPath,
        ];
        if (!empty($request->description)) {
            $postData['description'] = $request->description;
        }

        $post = Post::create($postData);

        $hashtags->saveHashtags($post->id, HashtagsLink::TAGGABLE_POST, $post->description);

        return $this->setStatusCode(201)->respondWithData($post);
    }

    public function update(Request $request, $post, HashtagRepositoryInterface $hashtagRepository)
    {
        $post = Post::find($post);
        if (!$this->belongsToAuthUser($post)) {
            return $this->respondForbidden('This post is not yours!');
        }

        if (isset($request->description)) {
            $post->description = $request->description;
            $hashtagRepository->saveHashtags($post->id, HashtagsLink::TAGGABLE_POST, $post->description);
        }

        // TODO: Thumbnails
        if (isset($request->thumbnail)) {
            // If post type_id is 1 (image) deny the thumbnail

            //

            $storage = \Storage::disk('public');
            if ($storage->exists($post->thumbnail)) {
                $storage->delete($post->thumbnail);
            }
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = $namePrefix . $thumbnail->getClientOriginalExtension();
            $path = "videos/post/{$userId}/{$currentYear}";

            $thumbnailPath = $storage->putFileAs($path, $thumbnail, $thumbnailName);


            $post->thumbnail = $request->thumbnail;
        }

        if (!$post->save()) {
            return $this->respondInternalError('Failed to save post');
        }

        return $this->respondWithData($post);
    }

    public function destroy($post)
    {
        $post = Post::find($post);
        if (!$this->belongsToAuthUser($post)) {
            return $this->respondForbidden('This post is not yours!');
        }

        if (!$post->delete()) {
            return $this->respondInternalError('Failed to delete post');
        }

        return $this->respondSuccess();
    }
}