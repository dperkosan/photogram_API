<?php

namespace App;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://photogramapi.test';

    /**
     * @var array of test user data
     */
    protected $testUserData;
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Get all data for the test user.
     * This is just an array and doesn't have an id set.
     *
     * @return array
     */
    protected function getTestUserData()
    {
        if (!$this->testUserData) {
            $this->testUserData = config('boilerplate.test_user');
        }
        return $this->testUserData;
    }

    protected function getTestUserEmail()
    {
        return $this->getTestUserData()['email'];
    }

    /**************************************
     * Authentication methods
     **************************************/

    /**
     * @param array $data
     *
     * @return string
     */
    protected function buildUrl(array $data) : string
    {
        return $this->url . '?' . http_build_query($data);
    }

    protected function buildAuthHeader(string $token) : array
    {
        return ['Authorization' => 'Bearer ' . $token];
    }

    protected function apiGetWithToken(array $data = null)
    {
        return $this->apiGet(DataProvider::getToken(), $data);
    }

    protected function apiGetWithoutHeader(array $data = null)
    {
        return $this->apiGet(null, $data);
    }

    protected function apiGet(string $token = null, array $data = null)
    {
        $headers = empty($token) ? [] : $this->buildAuthHeader($token);
        $url = empty($data) ? $this->url : $this->buildUrl($data);

        return $this->get($url, $headers);
    }
}
