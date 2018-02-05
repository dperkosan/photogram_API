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
    }

    public function getData()
    {
        return $this->endpoints;
    }

    protected function setupEndpoints()
    {
        // AUTH
        $this->generateEndpoint('/api/auth/signup', static::POST, [
          'username', 'email', 'name', 'password', 'password_confirmation',
        ]);
        $this->generateEndpoint('/api/auth/login', static::POST, [
          'email', 'password',
        ]);

        // POST
        $this->generateEndpoint('/api/posts', static::GET, [
          'amount', 'page', 'user_id', 'username',
        ]);
        $this->generateEndpoint('/api/posts/{id}');
        $this->generateEndpoint('/api/posts', static::POST, [
          'media', 'thumbnail', 'description',
        ]);
        $this->generateEndpoint('/api/posts', static::PATCH, [
          'thumbnail', 'description',
        ]);
        $this->generateEndpoint('/api/posts/{id}', static::DELETE);

        return $this;
    }

    protected function generateEndpoint($url, $method = self::GET, array $params = [])
    {
        $this->endpoints[] = [
          'method'     => $method,
          'url'        => $url,
          'parameters' => $this->pluckParams($params),
        ];
    }

    protected function pluckParams(array $keys)
    {
        if (!$keys) {
            return [];
        }

        return array_pluck($this->parameters, $keys);
    }

    protected function allParams()
    {
        return [
            // PAGINATION
            'amount'                => [
              'description'     => 'How many records to fetch',
              'possible_values' => '1 - infinity',
            ],
            'page'                  => [
              'description'     => 'start at amount * (page - 1)',
              'possible_values' => '1 - infinity',
            ],
            // USER
            'user_id'               => [
              'description'     => 'filter only records with user_id',
              'possible_values' => '1 - infinity',
            ],
            'username'              => [
              'description'     => 'filter only records from user with this username',
              'possible_values' => 'string',
            ],
            'name'                  => [
              'description'     => 'full name',
              'possible_values' => 'string',
            ],
            'email'                 => [
              'description'     => 'just email',
              'possible_values' => 'string',
            ],
            'password'              => [
              'description'     => 'must have lowercase letter, uppercase letter, number, special symbol, just kidding it can be only one character if you want',
              'possible_values' => 'string',
            ],
            'password_confirmation' => [
              'description'     => 'same as password',
              'possible_values' => 'string',
            ],
            // LIKE
            'likable_id'            => [
              'description'     => 'id of the likable',
              'possible_values' => 'string',
            ],
            'likable_type'          => [
              'description'     => 'filter only records from user with this username',
              'possible_values' => 'string',
            ],
            // POST
            'media'                 => [
              'description'     => 'file that can bean image or a video',
              'possible_values' => 'image or video',
            ],
            'thumbnail'             => [
              'description'     => 'file that is used when media is video',
              'possible_values' => 'image',
            ],
            'description'           => [
              'description'     => 'description of the post',
              'possible_values' => 'string up to 2200 characters',
            ],
        ];
    }
}