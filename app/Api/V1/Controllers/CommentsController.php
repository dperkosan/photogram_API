<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\CommentPaginationRequest;
use App\Api\V1\Requests\CommentRequest;
use App\Comment;
use App\Interfaces\CommentRepositoryInterface;
use App\Interfaces\MediaRepositoryInterface;
use Illuminate\Http\Request;

class CommentsController extends ApiController
{
    public function index(CommentPaginationRequest $request, CommentRepositoryInterface $commentRepository, MediaRepositoryInterface $mediaRepo)
    {
        $comments = $commentRepository->getComments($request->post_id, $request->amount, $request->page);

        $commentRepository->addAuthLike($comments, $this->authUser()->id);
        $mediaRepo->addThumbsToUsers($comments, 'user_image');

        return $this->respondWithData($comments);
    }

    public function store(CommentRequest $request, CommentRepositoryInterface $commentRepository, MediaRepositoryInterface $mediaRepo)
    {
        $this->dLogWithTime('Starting...');

        $commentData = $request->only(['body', 'post_id', 'reply_user_id', 'reply_username']);
        $commentData['user_id'] = $this->authUser()->id;

        $comment = $commentRepository->create($commentData);
        $this->dLogWithTime('Comment inserted in db');

        $fullComment = $commentRepository->getComment($comment->id);
        $commentRepository->addAuthLike([$fullComment], $this->authUser()->id);
        $mediaRepo->addThumbsToUsers($fullComment, 'user_image');

        $this->dLogWithTime('Responding...');
        
        return $this->respondWithData($fullComment);
    }

    public function update(Request $request, $comment, CommentRepositoryInterface $commentRepository, MediaRepositoryInterface $mediaRepo)
    {
        $this->validate($request, [
            'body' => 'nullable|string',
            'reply_user_id' => 'nullable|integer',
            'reply_username' => 'nullable|string',
        ]);
        $comment = Comment::find($comment);

        if (!$this->belongsToAuthUser($comment)) {
            return $this->respondForbidden('This comment is not yours!');
        }

        $commentSaved = $commentRepository->save($comment, $request->only([
            'body', 'reply_user_id', 'reply_username',
        ]));

        if (!$commentSaved) {
            return $this->respondInternalError('Failed to save comment');
        }

        $fullComment = $commentRepository->getComment($comment->id);
        $commentRepository->addAuthLike([$fullComment], $this->authUser()->id);
        $mediaRepo->addThumbsToUsers($fullComment, 'user_image');

        return $this->respondWithData($fullComment);
    }

    public function destroy($comment)
    {
        $comment = Comment::find($comment);

        $canDelete = $this->belongsToAuthUser($comment) || $this->belongsToAuthUser(\App\Post::find($comment->post_id));
        if (!$canDelete) {
            return $this->respondForbidden('This comment is not yours!');
        }

        // Delete polymorphic relations cause they don't delete themselves through foreign keys
        $comment->likes()->delete();
        $comment->hashtags()->delete();

        if (!$comment->delete()) {
            return $this->respondInternalError('Failed to delete comment');
        }

        return $this->respondSuccess();
    }
}