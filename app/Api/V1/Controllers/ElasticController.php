<?php

namespace App\Api\V1\Controllers;

use App\Post;
use Illuminate\Http\Request;

class ElasticController extends ApiController
{
    public function testIndexPosts()
    {
        $posts = Post::with('comments')->where('id', '>', 495)->get();

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
        $results = Post::searchByQuery(array('match' => array('description' => $q)));

        return $this->respondWithData($results);
    }


    public function indexPosts()
    {

    }
}