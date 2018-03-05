<?php

namespace App;

/**
 * Class DataProvider
 *
 * Provides data that is used in tests. The implementation is made to generate data only once,
 * and on subsequent requests with get methods just returns the data. Like mini singleton for each attribute.
 *
 * To add a new property that can be retrieved from this class implement two methods: getter and generator.
 * For example, token is implemented with getToken() and generateToken().
 *
 * The getter is always the same. Only the suffix changes in the method namem like:
 * getFoo(), getBar() and it is always implemented the same way:
 * return static::getInstance()->resolve('foo'); or
 * return static::getInstance()->resolve('bar');
 *
 * The generator is specific to each property and it represents the logic for generating the data.
 * Check generateToken() to see an example.
 *
 * OR, YOU JUST IMPLEMENT A CUSTOM GETTER METHOD AND DON'T BOTHER WITH THE RESOLVE() FUNCTION...
 */
class DataProvider
{
    protected $data;

    /**
     * @var static
     */
    protected static $instance;

    protected function __construct()
    {
        $this->data = [];
    }

    protected static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    protected function resolve($name)
    {
        if (!array_key_exists($name, $this->data)) {
            $this->{'generate'.ucfirst($name)}();
        }

        return $this->data[$name];
    }

    /*
    |--------------------------------------------------------------------------
    | Data properties / keys
    |--------------------------------------------------------------------------
    |
    | Below are methods for every data property.
    */


    public static function getToken()
    {
        return static::getInstance()->resolve('token');
    }

    protected function generateToken()
    {
//        echo "\nGenerating token...\n";
        $email = config('boilerplate.test_user')['email'];

        $user = User::where('email', $email)->first();

        $this->data['token'] = \JWTAuth::fromUser($user);
    }
}