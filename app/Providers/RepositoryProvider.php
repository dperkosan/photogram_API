<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Interfaces\PostRepositoryInterface', 'App\Repositories\PostRepository');
        $this->app->bind('App\Interfaces\FollowerRepositoryInterface', 'App\Repositories\FollowerRepository');
        $this->app->bind('App\Interfaces\UserRepositoryInterface', 'App\Repositories\UserRepository');

    }
}
