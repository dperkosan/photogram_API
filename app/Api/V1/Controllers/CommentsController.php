<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\CommentPaginationRequest;
use App\Api\V1\Requests\CommentRequest;
use App\Comment;
use App\Interfaces\CommentRepositoryInterface;

class CommentsController extends ApiController
{
    public function index(CommentPaginationRequest $request, CommentRepositoryInterface $commentsRepository)
    {
        $comments = $commentsRepository->getComments($request->post_id, $request->amount, $request->page);

        $commentsRepository->addAuthLike($comments, $this->authUser()->id);

        return $this->respondWithData($comments);
    }

    public function store(CommentRequest $request)
    {
        $commentData = $request->only(['body', 'post_id', 'comment_id']);

        $commentData['user_id'] = $this->authUser()->id;

        Comment::create($commentData);

        return $this->respondSuccess();
    }

    public function destroy(Comment $comment)
    {
        if (!$this->belongsToAuthUser($comment)) {
            return $this->respondForbidden('This like is not yours!');
        }

        if (!$comment->delete()) {
            return $this->respondInternalError('Failed to delete like');
        }

        return $this->respondSuccess();
    }
}