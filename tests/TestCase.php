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

    protected $testUser;
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

    protected function getTestUser()
    {
        if (!$this->testUser) {
            $this->testUser = config('boilerplate.test_user');
        }
        return $this->testUser;
    }

    protected function getTestUserEmail()
    {
        return $this->getTestUser()['email'];
    }
}
