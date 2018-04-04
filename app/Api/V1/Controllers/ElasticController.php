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

            // starts with
            //$results = User::searchByQuery(array('prefix' => array('username' => $query)));

            // contains
            $results = User::searchByQuery(array('regexp' => array('username' => '.*' . $query . '.*')));
        } else if ($symbol === '#') {
            $query = substr($q, 1);

            // starts with
            //$results = Hashtag::searchByQuery(array('prefix' => array('name' => $query)));

            // contains
            $results = Hashtag::searchByQuery(array('regexp' => array('name' => '.*' . $query . '.*')));
        } else {
            // exact word match
            // $results = Post::searchByQuery(array('match' => array('description' => $q))); 

            // contains
            $results = User::searchByQuery(array('regexp' => array('username' => '.*' . $query . '.*')));
        }

        return $this->respondWithData($results);
    }

    public function indexUsers()
    {
        return User::addAllToIndex();
    }

    public function indexHashtags()
    {
        return Hashtag::addAllToIndex();
    }
}