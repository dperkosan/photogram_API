<?php

namespace App\Repositories;

use App\Follower;
use App\Interfaces\CommentRepositoryInterface;
use App\Interfaces\FollowerRepositoryInterface;
use App\Interfaces\LikeRepositoryInterface;
use App\Post;
use App\Interfaces\PostRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PostRepository extends Repository implements PostRepositoryInterface
{
    /**
     * @var \App\Post
     */
    protected $post;
    protected $followerRepository;
    protected $commentRepository;

    const TYPE_IMAGE = 1;
    const TYPE_VIDEO = 2;

    public function __construct(Post $post, FollowerRepositoryInterface $followerRepository, CommentRepositoryInterface $commentRepository)
    {
        $this->post = $post;
        $this->followerRepository = $followerRepository;
        $this->commentRepository = $commentRepository;
    }

    public function getPost($id)
    {
        $posts = $this->fullQuery()->whereKey($id)->first();
        $this->addComments($posts);

        return $posts;
    }

    /**
     * Get latest posts
     *
     * @param integer $amount number of posts to return
     * @param int     $page
     * @param null    $userId
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPosts($amount, $page = 1, $userId = null)
    {
        return $this->standardFetch($amount, $page, function ($query) use ($userId) {
            if ($userId) {
                $query->where('user_id', $userId);
            }
        });
    }

    /**
     * This is for the home page, so it returns posts from followed users
     *
     * @param int $userId
     * @param int $amount
     * @param int $page
     *
     * @return Collection
     */
    public function newsFeed($userId, $amount, $page = 1)
    {
        $followedIds = Follower::where('follower_id', $userId)->pluck('followed_id');
        
        return $this->standardFetch($amount, $page, function ($query) use ($followedIds) {
            $query->whereIn('user_id', $followedIds);
        });
    }

    /**
     * @param  int  $amount
     * @param  int  $page
     * @param \Closure $callable add more query functions here if you want
     *
     * @return Collection
     */
    protected function standardFetch($amount, $page, $callable = null)
    {
        $query = $this->fullQuery($amount, $page);

        if ($callable) call_user_func($callable, $query);

        $posts = $query->get();

        $this->addComments($posts);

        return $posts;
    }

    /**
     * @param Collection|\App\Post $posts
     * @param int                  $limit
     *
     * @return Post|Collection|null
     */
    public function addComments($posts, $limit = 5)
    {
        if (!$posts) {
            return null;
        } else if ($posts instanceof Post) {
            $this->addCommentsToSingePost($posts, $limit);
        } else {
            foreach ($posts as $post) {
                $this->addCommentsToSingePost($post, $limit);
            }
        }

        return $posts;
    }

    /**
     * @param \App\Post $post
     * @param int       $limit
     */
    public function addCommentsToSingePost($post, $limit = 5)
    {
        if (!isset($post->comments_count) || $post->comments_count > 0) {
            $post->load(['comments' => function ($query) use ($limit) {
                $query
                    ->select([
                        'comments.*',
                        'comments_users.username',
                        'comments_users.image as user_image',
                        'reply_users.id as reply_user_id',
                        'reply_users.username as reply_username',
                    ])
                    ->join('users as comments_users', 'comments_users.id', '=', 'comments.user_id')
                    ->leftJoin('users as reply_users', 'reply_users.id', '=', 'comments.reply_user_id')
                    ->withCount('likes');
            }]);
        }
    }

//    /**
//     * @param Collection|\App\Post $posts
//     */
//    public function addThumbs($posts)
//    {
//        $thumbs = config('boilerplate.thumbs.post');
//
//        foreach ($posts as $post) {
//
//            if ($post->type_id === Post::TYPE_IMAGE) {
//                $post->thumbs = [];
//                if (strpos($post->media, '[~FORMAT~]') !== false) {
//
//                    $arr_thumbs = [];
//                    foreach ($thumbs as $thumb_name => $thumb_format) {
//                        $arr_thumbs[$thumb_name] = str_replace('[~FORMAT~]', $thumb_name, $post->media);
//                    }
//                    $arr_thumbs['orig'] = str_replace('[~FORMAT~]', 'orig', $post->media);
//                    $post->thumbs = $arr_thumbs;
//                }
//            }
//        }
//    }

    /**
     * Adds is_liked (by auth user) to every post and nested comment
     *
     * @param Collection $posts
     * @param integer    $userId
     *
     * @return Collection
     */
    public function addAuthLike($posts, $userId)
    {
        return app(LikeRepositoryInterface::class)->addAuthLikeToPosts($posts, $userId);
    }

    /**
     * Post with user, comments*, comments_count, likes_count, order, offset, limit
     *
     * comments* with user, likes_count, limit 5
     *
     * @param int $amount
     * @param int $page
     *
     * @return \Illuminate\Database\Eloquent\Builder|mixed
     */
    private function fullQuery($amount = 1, $page = 1)
    {
        $offset = $this->calcOffset($amount, $page);

        return $this->post
          ->select(['posts.*', 'users.username', 'users.image as user_image'])
          ->join('users', 'users.id', '=' , 'posts.user_id')
          ->withCount('comments')
          ->withCount('likes')
          ->orderBy('created_at', 'DESC')
          ->offset($offset)
          ->limit($amount);
    }
}