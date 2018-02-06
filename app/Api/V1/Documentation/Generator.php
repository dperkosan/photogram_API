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
        // AUTH
        $this->generateEndpoint('/auth/signup', 'Sign up and you will receive a confirmation email.', static::POST, [
          'username', 'email', 'name', 'password', 'password_confirmation',
        ]);
        $this->generateEndpoint('/auth/login', 'Log in and you will receive a token. Put in as a header {"Authorization" : "Bearer " + token}', static::POST, [
          'email', 'password',
        ]);
        $this->generateEndpoint('/auth/recovery', 'Sends an email with a link to reset password', static::POST, [
          'email'
        ]);
        $this->generateEndpoint('/auth/reset', 'Inserts a new password by the recovery link. Not tested', static::POST, [
          'email', 'password', 'password_confirmation', 'token'
        ]);

        $this->generateEndpoint('/signup/confirmation', 'This is used automatically by the confirm email button.');

        $this->generateEndpoint('/config', 'Lots of stuff about the app.');


        $this->generateEndpoint('/users/exists', 'Check if a user exists. Send any of the parameters.', static::GET, [
          'username', 'email', 'name', 'gender_id'
        ]);

        $this->generateEndpoint('/users/find', 'Find a user. Send any of the parameters.', static::GET, [
          'username', 'email', 'name', 'gender_id'
        ]);

        $this->generateEndpoint('/users/auth', 'Get data for authenticated user.');

        $this->generateEndpoint('/users/auth/image', 'Update auth user\'s image.', static::POST, [
          'image',
        ]);

        $this->generateEndpoint('/users/auth/update', 'Update auth user\'s information.', static::PATCH, [
          'name', 'gender_id', 'phone', 'about', 'username',
        ]);

        $this->generateEndpoint('/followers', 'Get users who follow you.');

        $this->generateEndpoint('/followings', 'Get users who you follow.');

        $this->generateEndpoint('/followers', 'Follow a user.', static::POST, [
          'user_id',
        ]);

        $this->generateEndpoint('/posts/test', 'Same as /posts but this one doesn\'t require auth.', static::GET);

        // POST
        $this->generateEndpoint('/posts', 'Get posts with pagination. user_id or username is optional.', static::GET, [
          'amount', 'page', 'user_id', 'username',
        ]);
        $this->generateEndpoint('/posts/{id}', 'Get a full post by it\'s id');
        $this->generateEndpoint('/posts', 'Create a new post. Send either image or video and thumbnail (optional).', static::POST, [
          'image', 'video', 'thumbnail', 'description',
        ]);
        $this->generateEndpoint('/posts', 'Edit a post. Only description and thumbnail can be changed.', static::PATCH, [
          'thumbnail', 'description',
        ]);
        $this->generateEndpoint('/posts/{id}', 'Delete a post.', static::DELETE);

        $this->generateEndpoint('/home', 'Same as /posts but this one doesn\'t require auth.', static::GET);

        $this->generateEndpoint('/comments', 'Get comments.', static::GET, [
          'post_id', 'comment_id', 'amount', 'page',
        ]);

        $this->generateEndpoint('/comments', 'Create a comment.', static::POST, [
          'post_id', 'comment_id', 'body',
        ]);

        $this->generateEndpoint('/comments', 'Change the body of a comment.', static::PATCH, [
          'body',
        ]);

        $this->generateEndpoint('/comments/{id}', 'Delete a comment.', static::DELETE);

        $this->generateEndpoint('/likes', 'Get likes.', static::GET, [
          'likable_id', 'likable_type', 'amount', 'page',
        ]);

        $this->generateEndpoint('/likes', 'Create a like.', static::POST, [
          'likable_id', 'likable_type',
        ]);

        $this->generateEndpoint('/likes/{id}', 'Delete a like.', static::DELETE);

        return $this;
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
            // PAGINATION
            [
              'name' => 'amount',
              'description'     => 'How many records to fetch',
              'possible_values' => 'integer',
            ],
            [
              'name' => 'page',
              'description'     => 'start at amount * (page - 1)',
              'possible_values' => 'integer',
            ],
            // USER
            [
              'name' => 'user_id',
              'description'     => 'id of a user',
              'possible_values' => 'integer',
            ],
            [
              'name' => 'username',
              'description'     => 'username of a user',
              'possible_values' => 'string',
            ],
            [
              'name' => 'name',
              'description'     => 'full name',
              'possible_values' => 'string',
            ],
            [
              'name' => 'email',
              'description'     => 'just email',
              'possible_values' => 'string',
            ],
            [
              'name' => 'password',
              'description'     => 'must have lowercase letter, uppercase letter, number, special symbol, just kidding it can be only one character if you want',
              'possible_values' => 'string',
            ],
            [
              'name' => 'password_confirmation',
              'description'     => 'same as password',
              'possible_values' => 'string',
            ],
            [
              'name' => 'gender_id',
              'description'     => 'id of user gender',
              'possible_values' => '1 - male, 2 - female, 3 - other',
            ],
            [
              'name' => 'phone',
              'description'     => 'Phone numbah',
              'possible_values' => 'string',
            ],
            [
              'name' => 'about',
              'description'     => 'Something about you',
              'possible_values' => 'string',
            ],
            // LIKE
            [
              'name' => 'likable_id',
              'description'     => 'id of the likable',
              'possible_values' => 'integer',
            ],
            [
              'name' => 'likable_type',
              'description'     => 'filter only records from user with this username',
              'possible_values' => 'integer',
            ],
            // POST
            [
              'name' => 'image',
              'description'     => 'uploaded image file',
              'possible_values' => 'image',
            ],
            [
              'name' => 'video',
              'description'     => 'uploaded video file',
              'possible_values' => 'video',
            ],
            [
              'name' => 'thumbnail',
              'description'     => 'image file that is used when video is uploaded',
              'possible_values' => 'image',
            ],
            [
              'name' => 'description',
              'description'     => 'description of the post',
              'possible_values' => 'string up to 2200 characters',
            ],
            // RECOVER PASSWORD
            [
              'name' => 'token',
              'description'     => 'this is a special case when resetting password, not tested, old code',
              'possible_values' => 'string',
            ],
            // COMMENT
            [
              'name' => 'post_id',
              'description'     => 'id of the post',
              'possible_values' => 'integer',
            ],
            [
              'name' => 'comment_id',
              'description'     => 'id of the comment',
              'possible_values' => 'integer',
            ],
            [
              'name' => 'body',
              'description'     => 'comment body',
              'possible_values' => 'string',
            ],
        ];
    }
}