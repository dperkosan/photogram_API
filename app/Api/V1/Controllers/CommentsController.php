<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\CommentPaginationRequest;
use App\Api\V1\Requests\CommentRequest;
use App\Comment;
use App\HashtagsLink;
use App\Interfaces\CommentRepositoryInterface;
use App\Interfaces\HashtagRepositoryInterface;
use App\Interfaces\MediaRepositoryInterface;
use Illuminate\Http\Request;

class CommentsController extends ApiController
{
    public function index(CommentPaginationRequest $request, CommentRepositoryInterface $commentRepository, MediaRepositoryInterface $mediaRepo)
    {
        $comments = $commentRepository->getComments($request->post_id, $request->comment_id, $request->amount, $request->page);

        $commentRepository->addAuthLike($comments, $this->authUser()->id);
        $mediaRepo->addThumbsToUsers($comments, 'user_image');

        return $this->respondWithData($comments);
    }

    public function store(CommentRequest $request, HashtagRepositoryInterface $hashtagRepository)
    {
        $commentData = $request->only(['body', 'post_id', 'comment_id']);
        $commentData['user_id'] = $this->authUser()->id;

        $comment = Comment::create($commentData);

        $hashtagRepository->saveHashtags($comment->id, HashtagsLink::TAGGABLE_COMMENT, $comment->body);

        return $this->respondWithData($comment);
    }

    public function update(Request $request, $comment, HashtagRepositoryInterface $hashtagRepository)
    {
        $this->validate($request, [
            'body' => 'required',
        ]);

        $comment = Comment::find($comment);

        if (!$this->belongsToAuthUser($comment)) {
            return $this->respondForbidden('This comment is not yours!');
        }

        if (isset($request->body)) {
            $comment->body = $request->body;
            $hashtagRepository->saveHashtags($comment->id, HashtagsLink::TAGGABLE_COMMENT, $comment->body);
        }

        if (!$comment->save()) {
            return $this->respondInternalError('Failed to save comment');
        }

        return $this->respondWithData($comment);
    }

    public function destroy($comment)
    {
        $comment = Comment::find($comment);

        $canDelete = $this->belongsToAuthUser($comment) || $this->belongsToAuthUser(\App\Post::find($comment->post_id));
        if (!$canDelete) {
            return $this->respondForbidden('This comment is not yours!');
        }

        if (!$comment->delete()) {
            return $this->respondInternalError('Failed to delete comment');
        }

        return $this->respondSuccess();
    }
}