<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
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
        $repositories = [
          'Post', 'Follower', 'User', 'Hashtag', 'Like', 'Comment'
        ];

        foreach ($repositories as $repo) {
            $this->app->bind("App\\Interfaces\\{$repo}RepositoryInterface", "App\\Repositories\\{$repo}Repository");

        }
    }
}
