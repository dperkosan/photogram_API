<?php

namespace App;

use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;

/**
 * Provides data that is used in tests. The implementation is made to generate data only once,
 * and on subsequent requests with get methods just returns the data. Like mini singleton for each attribute.
 *
 * To add a new property that can be retrieved from this class implement two methods:
 * static method getter and non-static method generator.
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
    /**
     * Contains keys:
     *
     *  test_user_data,
     *  test_user_email,
     *  test_user,
     *  token,
     *  header
     *
     * and maybe more.
     *
     * @var array of properties
     */
    protected $data;

    /**
     * @var static
     */
    protected static $instance;

    protected function __construct()
    {
        $this->data = [];
    }

    protected static function getInstance() : self
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    protected function resolve($name)
    {
        if (!array_key_exists($name, $this->data)) {
            $this->data[$name] = $this->{'generate'.ucfirst(camel_case($name))}();
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

    public static function getTestUserData() : array
    {
        return static::getInstance()->resolve('test_user_data');
    }

    protected function generateTestUserData() : array
    {
        return config('boilerplate.test_user');
    }

    public static function getTestUserEmail() : string
    {
        return static::getInstance()->resolve('test_user_email');
    }

    protected function generateTestUserEmail() : string
    {
        return static::getTestUserData()['email'];
    }

    public static function getTestUser() : User
    {
        return static::getInstance()->resolve('test_user');
    }

    protected function generateTestUser() : User
    {
        $email = static::getTestUserData()['email'];

        return User::where('email', $email)->first();
    }

    public static function getToken() : string
    {
        return static::getInstance()->resolve('token');
    }

    protected function generateToken() : string
    {
        return \JWTAuth::fromUser(static::getTestUser());
    }

    public static function getHeader() : array
    {
        return static::getInstance()->resolve('header');
    }

    protected function generateHeader() : array
    {
        return ['Authorization' => 'Bearer ' . static::getToken()];
    }

    public static function getFakeImage() : File
    {
        return static::getInstance()->resolve('fake_image');
    }

    protected function generateFakeImage() : File
    {
        return UploadedFile::fake()->image('fake_image.png');
    }
}