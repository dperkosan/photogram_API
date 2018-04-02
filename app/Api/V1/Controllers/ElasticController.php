<?php

namespace App\Api\V1\Controllers;

use App\Hashtag;
use App\Post;
use App\User;
use Illuminate\Http\Request;

class ElasticController extends ApiController
{

    public function testIndexPosts()
    {
        $posts = Post::with('comments')->where('id', '<', 6)->get();

        $posts->addToIndex();

        return $this->respondWithData($posts);
    }


    public function testIndexUsers()
    {
        $posts = User::where('id', '<', 6)->get();

        $posts->addToIndex();

        return $this->respondWithData($posts);
    }


    public function testIndexHashtags()
    {
        $posts = User::where('id', '<', 6)->get();

        $posts->addToIndex();

        return $this->respondWithData($posts);
    }


    public function search(Request $request)
    {
        $this->validate($request, [
            'q' => 'string|min:3|max:100'
        ]);
        $q = $request->q;

//        $results = Post::search($q);

        $symbol = substr($q, 0, 1);

        if ($symbol === '@') {
            $query = substr($q, 1);
            $results = User::searchByQuery(array('match' => array('username' => $query)));
        } else if ($symbol === '#') {
            $query = substr($q, 1);
            $results = Hashtag::searchByQuery(array('match' => array('name' => $query)));
        } else {
            $results = Post::searchByQuery(array('match' => array('description' => $q)));
        }

        return $this->respondWithData($results);
    }


    public function indexPosts()
    {

    }
}