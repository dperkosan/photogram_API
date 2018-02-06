<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\CommentPaginationRequest;
use App\Api\V1\Requests\CommentRequest;
use App\Comment;
use App\Interfaces\CommentRepositoryInterface;
use Illuminate\Http\Request;

class CommentsController extends ApiController
{
    public function index(CommentPaginationRequest $request, CommentRepositoryInterface $commentsRepository)
    {
        $comments = $commentsRepository->getComments($request->post_id, $request->comment_id, $request->amount, $request->page);

        $commentsRepository->addAuthLike($comments, $this->authUser()->id);

        return $this->respondWithData($comments);
    }

    public function store(CommentRequest $request)
    {
        $commentData = $request->only(['body', 'post_id', 'comment_id']);
        // TODO: save hashtags ffs
        $commentData['user_id'] = $this->authUser()->id;

        Comment::create($commentData);

        return $this->respondSuccess();
    }

    public function update(Request $request, $comment)
    {
        $comment = Comment::find($comment);

        if (!$this->belongsToAuthUser($comment)) {
            return $this->respondForbidden('This comment is not yours!');
        }
        // TODO: save hashtags ffs

        if (!$comment->update($request->only(['body']))) {
            return $this->respondInternalError('Failed to save comment');
        }

        return $this->respondSuccess();
    }

    public function destroy($comment)
    {
        $comment = Comment::find($comment);

        if (!$this->belongsToAuthUser($comment)) {
            return $this->respondForbidden('This like is not yours!');
        }

        if (!$comment->delete()) {
            return $this->respondInternalError('Failed to delete comment');
        }

        return $this->respondSuccess();
    }
}