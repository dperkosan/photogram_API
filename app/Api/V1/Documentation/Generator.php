<?php

namespace App\Api\V1\Documentation;

class Generator
{
    const GET = 'GET';
    const POST = 'POST';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';

    protected $parameters;

    protected $endpoints;

    public $intro = 'All of the endpoints should be listed here. Prepend every endpoint with /api. Open /api/config to see a lot of configuration...';

    /**
     * @var Generator $instance
     */
    protected static $instance;

    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    protected function __construct()
    {
        $this->parameters = $this->allParams();
        $this->setupEndpoints();
    }

    public function getData()
    {
        return $this->endpoints;
    }

    protected function setupEndpoints()
    {
        $this->get('/config', 'Lots of stuff about the app.');
        // AUTH
        $this->post('/auth/signup', 'Sign up and you will receive a confirmation email.', [
          'username', 'email', 'name', 'password', 'password_confirmation',
        ]);
        $this->post('/auth/login', 'Log in and you will receive a token. Put in as a header {"Authorization" : "Bearer " + token}', [
          'email', 'password',
        ]);
        $this->post('/auth/recovery', 'Sends an email with a link to reset password', [
          'email'
        ]);
        $this->post('/auth/reset', 'Inserts a new password by the recovery link. Not tested', [
          'email', 'password', 'password_confirmation', 'token'
        ]);

        $this->get('/signup/confirmation', 'This is used automatically by the confirm email button.');

        $this->get('/users/exists', 'Check if a user exists. Send any of the parameters. For example you can see if a user named John doe with gender_id of 1 exists', [
          'username', 'email', 'name', 'gender_id'
        ]);

        $this->get('/users/find', 'Find a user. Send any of the parameters. For example you can find a user named John doe with gender_id of 1. If you pass an "id" other params are ignored.', [
          'id', 'username', 'email', 'name', 'gender_id'
        ]);

        $this->get('/users/auth', 'Get data for authenticated user, like image, name, gender, etc.');

        $this->post('/users/auth/image', 'Update (change) auth user\'s image.', [
          'image',
        ]);

        $this->patch('/users/auth/update', 'Update auth user\'s information. The parameters are the stuff that can be updated.', [
          'name', 'gender_id', 'phone', 'about', 'username',
        ]);

        $this->get('/followers', 'Get users who follow you.');

        $this->get('/followings', 'Get users who you follow.');

        $this->post('/followers', 'Follow a user with user_id', [
          'user_id',
        ]);

        $this->delete('/followers/user_id', 'Unfollow a user with user_id');

        $this->get('/followers/mutual', 'Get users that are mutual followers for you and the user with user_id.', [
          'user_id', 'amount', 'page'
        ]);

        $this->get('/posts/test', 'Same as /posts but this one doesn\'t require auth. This is just for testing.');

        // POST
        $this->get('/posts', 'Get posts with pagination. For getting posts from one user either pass user_id or username. For getting posts from all users that the auth user follows, pass the news_feed parameter (it can be empty lke ...&news_feed&...). In the latter case user_id or username is ignored.', [
          'amount', 'page', 'user_id', 'username', 'news_feed'
        ]);
        $this->get('/posts/post_id', 'Get a full post by it\'s id');
        $this->post('/posts', 'Create a new post. Send either image or video and thumbnail (optional).', [
          'image', 'video', 'thumbnail', 'description',
        ]);
        $this->patch('/posts/post_id', 'Edit a post. Only description and thumbnail can be changed.', [
          'thumbnail', 'description',
        ]);
        $this->delete('/posts/post_id', 'Delete a post.');

        $this->get('/home', 'Same as /posts with "news_feed" param passed');

        $this->get('/comments', 'Get comments from a post with id of "post_id". The parameter comment_id is deprecated', [
          'post_id', 'comment_id', 'amount', 'page',
        ]);

        $this->post('/comments', 'Create a comment. The parameter comment_id is deprecated', [
          'post_id', 'reply_username', 'body',
        ]);

        $this->patch('/comments', 'Change the body of a comment. This is used for editing the comment.', [
          'body',
        ]);

        $this->delete('/comments/comment_id', 'Delete a comment.');

        $this->get('/likes', 'Get likes. See "/api/config" for config. Likable entity is an entity that can be liked. Every entity has a type (post, comment, etc.)', [
          'likable_id', 'likable_type', 'amount', 'page',
        ]);

        $this->post('/likes', 'Create a like. See "/api/config" for config.', [
          'likable_id', 'likable_type',
        ]);

        $this->delete('/likes/like_id', 'Delete a like.');

        $this->get('/search', 'Search for a username or hashtag. If query string starts with @ usernames will be searched. If it starts with # hashtags will be searched.', [
            'q',
        ]);

        return $this;
    }

    protected function get($url, $description = null, array $params = [])
    {
        $this->generateEndpoint($url, $description, self::GET, $params);
    }

    protected function post($url, $description = null, array $params = [])
    {
        $this->generateEndpoint($url, $description, self::POST, $params);
    }

    protected function patch($url, $description = null, array $params = [])
    {
        $this->generateEndpoint($url, $description, self::PATCH, $params);
    }

    protected function delete($url, $description = null, array $params = [])
    {
        $this->generateEndpoint($url, $description, self::DELETE, $params);
    }

    protected function generateEndpoint($url, $description = null, $method = self::GET, array $params = [])
    {
        $this->endpoints[] = [
          'method'      => $method,
          'description' => $description,
          'url'         => $url,
          'parameters'  => $this->takeParams($params),
        ];
    }

    protected function takeParams(array $names)
    {
        if (!$names) {
            return [];
        }

        $params = [];

        foreach ($names as $name) {
            foreach ($this->parameters as $parameter) {
                if ($parameter['name'] === $name) {
                    $params[] = $parameter;
                    break;
                }
            }
        }

        return $params;
    }

    protected function allParams()
    {
        return [
            [
                'name'            => 'id',
                'description'     => 'Primary key of a resource',
                'possible_values' => 'integer',
            ],
            // PAGINATION
            [
              'name'            => 'amount',
              'description'     => 'How many records to fetch',
              'possible_values' => 'integer',
            ],
            [
              'name'            => 'page',
              'description'     => 'Start at amount * (page - 1). For example if amount is 10 and page is 2 then items from 11 to 20 will be returned',
              'possible_values' => 'integer',
            ],
            // USER
            [
              'name'            => 'user_id',
              'description'     => 'id of a user',
              'possible_values' => 'integer',
            ],
            [
              'name'            => 'username',
              'description'     => 'an unique string, classic username',
              'possible_values' => 'string|max:25',
            ],
            [
              'name'            => 'email',
              'description'     => 'an unique string, classic email',
              'possible_values' => 'string|max:100',
            ],
            [
              'name'            => 'name',
              'description'     => 'represents the full name of the user',
              'possible_values' => 'string|max:100',
            ],
            [
              'name'            => 'password',
              'description'     => 'a word for passing',
              'possible_values' => 'string|min:6|max:60',
            ],
            [
              'name'            => 'password_confirmation',
              'description'     => 'must be same as password',
              'possible_values' => 'string|min:6|max:60',
            ],
            [
              'name'            => 'gender_id',
              'description'     => 'id of user gender',
              'possible_values' => '1 - male, 2 - female, 3 - other',
            ],
            [
              'name'            => 'phone',
              'description'     => 'Phone numbah',
              'possible_values' => 'string',
            ],
            [
              'name'            => 'about',
              'description'     => 'Something about you',
              'possible_values' => 'string',
            ],
            // LIKE
            [
              'name'            => 'likable_id',
              'description'     => 'id of the likable',
              'possible_values' => 'integer',
            ],
            [
              'name'            => 'likable_type',
              'description'     => 'filter only records from user with this username',
              'possible_values' => 'integer',
            ],
            // POST
            [
              'name'            => 'image',
              'description'     => 'uploaded image file',
              'possible_values' => 'image',
            ],
            [
              'name'            => 'video',
              'description'     => 'uploaded video file',
              'possible_values' => 'video',
            ],
            [
              'name'            => 'thumbnail',
              'description'     => 'image file that is used when video is uploaded',
              'possible_values' => 'image',
            ],
            [
              'name'            => 'description',
              'description'     => 'description of the post',
              'possible_values' => 'string|max:2200',
            ],
            [
              'name'            => 'news_feed',
              'description'     => 'to get posts from followed users',
              'possible_values' => 'values ignored, it just needs to be set',
            ],
            // RECOVER PASSWORD
            [
              'name'            => 'token',
              'description'     => 'this is a special case when resetting password, not tested, old code',
              'possible_values' => 'string',
            ],
            // COMMENT
            [
              'name'            => 'post_id',
              'description'     => 'id of the post',
              'possible_values' => 'integer',
            ],
            [
              'name'            => 'comment_id',
              'description'     => 'id of the comment (deprecated)',
              'possible_values' => 'integer',
            ],
            [
              'name'            => 'body',
              'description'     => 'comment body',
              'possible_values' => 'string',
            ],
            [
                'name'            => 'q',
                'description'     => 'query string to search for',
                'possible_values' => 'string starting with @ or #',
            ],
            [
                'name'            => 'reply_username',
                'description'     => 'username to whom the comment is replied',
                'possible_values' => 'string|max:25',
            ],
        ];
    }
}