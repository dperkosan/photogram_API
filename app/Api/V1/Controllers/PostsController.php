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

    public function __construct(JWTAuth $jwtAuth, PostRepositoryInterface $posts)
    {
        $this->jwtAuth = $jwtAuth;
        $this->posts = $posts;
    }

    public function newsFeed(PostPaginationRequest $request, MediaRepositoryInterface $mediaRepo)
    {
        return $this->baseNewsFeed($request, $mediaRepo);
    }

    private function baseNewsFeed($request, MediaRepositoryInterface $mediaRepo)
    {
        $userId = $this->authUser()->id;

        $posts = $this->posts->newsFeed($userId, $request->amount, $request->page);

        $this->posts->addAuthLike($posts, $userId);

        $mediaRepo->addThumbsToPosts($posts);
        $mediaRepo->addThumbsToUsers($posts, 'user_image');

        return $this->respondWithData($posts);
    }

    public function index(PostPaginationRequest $request, MediaRepositoryInterface $mediaRepo)
    {
        if (isset($request->news_feed)) {
            return $this->baseNewsFeed($request, $mediaRepo);
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
        $mediaRepo->addThumbsToPosts($posts);
        $mediaRepo->addThumbsToUsers($posts, 'user_image');

        return $this->respondWithData($posts);
    }

    public function show($post, MediaRepositoryInterface $mediaRepo)
    {
        $posts = $this->posts->getPost($post);

        $authUser = $this->authUser();
        if ($authUser) {
            $this->posts->addAuthLike($posts, $authUser->id);
        }
        $mediaRepo->addThumbsToPosts($posts);
        $mediaRepo->addThumbsToUsers($posts, 'user_image');

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
            $mediaPath = $mediaRepo->savePostVideoOrThumbnail($media, $user);
        }

        $thumbnailPath = null;
        if (!$isImage && $request->has('thumbnail')) {
            $thumbnailPath = $mediaRepo->savePostVideoOrThumbnail($request->file('thumbnail'), $user);
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

    public function update(Request $request, $post, MediaRepositoryInterface $mediaRepo, HashtagRepositoryInterface $hashtagRepository)
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
            $user = $this->authUser();

            if ($post->type_id === Post::TYPE_VIDEO) {
                $thumbnailPath = $mediaRepo->savePostVideoOrThumbnail($request->thumbnail, $user);

                // If thumbnail image is saved delete the old one and set the new one for the post
                if ($thumbnailPath) {
                    $mediaRepo->deleteFiles($post->thumbnail);
                    $post->thumbnail = $thumbnailPath;
                }
            }
        }

        if (!$post->save()) {
            return $this->respondInternalError('Failed to save post');
        }

        return $this->respondWithData($post);
    }

    public function destroy($post, MediaRepositoryInterface $mediaRepo)
    {
        $post = Post::find($post);
        if (!$this->belongsToAuthUser($post)) {
            return $this->respondForbidden('This post is not yours!');
        }

        $success = false;
        if ($post->type_id === Post::TYPE_IMAGE) {
            $success = $mediaRepo->deletePostImage($post->image);
        } else if ($post->type_id === Post::TYPE_VIDEO) {
            $success = $mediaRepo->deleteFiles($post->video);
        }

        if (!$post->delete()) {
            return $this->respondInternalError('Failed to delete post');
        }

        return $this->respondWithMessage('Post deleted; image/video files deletion ' . ($success ? 'succeeded.' : 'failed!'));
    }
}