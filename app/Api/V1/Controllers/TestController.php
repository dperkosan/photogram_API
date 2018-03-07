<?php

namespace App\Api\V1\Controllers;

use \App\Post;
use \App\Follower;
use Dingo\Api\Http\Request;
use Tymon\JWTAuth\Providers\JWT\JWTInterface;

class TestController extends ApiController
{
    public function index()
    {
        $start = microtime(true);
        foreach (range(1, 1) as $i) {
            Post::where('user_id', $i)->count();
            Follower::where('followed_id', $i)->count();
            Follower::where('follower_id', $i)->count();
        }
        $end = microtime(true);

        $data['1'] = $end - $start;

        $start = microtime(true);
        foreach (range(1, 1) as $i) {
            \DB::table('posts')->where('user_id', $i)->count();
            \DB::table('followers')->where('followed_id', $i)->count();
            \DB::table('followers')->where('follower_id', $i)->count();
        }
        $end = microtime(true);

        $data['2'] = $end - $start;

        return $this->respondWithData($data);
//        $token = '
//            eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly9waG90b2dyYW1hcGkudGVzdC9hcGkvYXV0aC9sb2dpbiIsImlhdCI6MTUyMDI2MjI2MiwiZXhwIjoxODM1NjIyMjYyLCJuYmYiOjE1MjAyNjIyNjIsImp0aSI6IlhRMzk3VFNSUXdpcTVBdVUifQ._-iAE87L3UoI3Ssb-jbJCfn5jRy6AKNNN4VQ4UIWMEM
//        ';
//        $payload = $jwt->decode(trim($token));
//
//        return $this->respondWithData($payload);
    }
}
