<?php

namespace App\Http\Middleware;

use Closure;
use Dingo\Api\Http\Response;

class ProfileJsonResponse
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if ($response instanceof Response && app()->bound('debugbar') && app('debugbar')->isEnabled()) {
            if (json_decode($response->morph()->getContent(), true)) {
                $response->setContent(json_decode($response->morph()->getContent(),
                    true) + ['_debugbar' => app('debugbar')->getData(),]);
            }

        }

        return $response;
    }
}