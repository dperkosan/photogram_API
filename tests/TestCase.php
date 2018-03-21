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
     * Url path, concatenated to the $baseUrl
     *
     * @var string
     */
    protected $path = '';

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

    protected function refreshApplicationIfNotRefreshed()
    {
        if (! $this->app) {
            $this->refreshApplication();
        }
    }

    /**************************************
     * Authentication methods
     **************************************/

    /**
     * @param array  $data
     * @param string $pathSuffix
     *
     * @return string
     */
    protected function buildUrl(array $data, $pathSuffix = '') : string
    {
        $url = $this->path . $pathSuffix;
        return empty($data) ? $url : $url . '?' . http_build_query($data);
    }

    protected function apiGet(array $data = [], string $pathSuffix = '', bool $includeTokenInHeader = true)
    {
        $url = $this->buildUrl($data, $pathSuffix);
        $headers = $includeTokenInHeader ? DataProvider::getHeader() : [];

        return $this->get($url, $headers);
    }


    protected function apiPost(array $data = [], string $pathSuffix = '', $headers = [], $includeTokenInHeader = true)
    {
        if ($includeTokenInHeader) {
            $headers = array_merge($headers, DataProvider::getHeader());
        }

        return $this->post($this->path . $pathSuffix, $data, $headers);
    }

    protected function apiPatch(array $data = [], $pathSuffix = '', $headers = [], $includeTokenInHeader = true)
    {
        if ($includeTokenInHeader) {
            $headers = array_merge($headers, DataProvider::getHeader());
        }

        $path = $this->path . $pathSuffix;

        return $this->patch($path, $data, $headers);
    }

    protected function apiDelete(int $id, $data = [], $headers = [], $includeTokenInHeader = true)
    {
        if ($includeTokenInHeader) {
            $headers = array_merge($headers, DataProvider::getHeader());
        }

        $path = $this->path . '/' . $id;

        return $this->delete($path, $data, $headers);
    }

    protected function paginationPropertyPageMissing()
    {
        return $this->apiGet([
            'amount' => 10,
        ])->assertStatus(422);
    }

    protected function paginationPropertyAmountMissing()
    {
        return $this->apiGet([
            'page' => 1,
        ])->assertStatus(422);
    }
}
