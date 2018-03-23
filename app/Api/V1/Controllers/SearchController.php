<?php

namespace App\Api\V1\Controllers;



use App\Hashtag;
use App\User;
use Illuminate\Http\Request;

class SearchController extends ApiController
{
    public function search(Request $request)
    {
        $this->validate($request, [
            'q' => 'string|min:3|max:100'
        ]);
        $q = $request->q;

        $symbol = substr($q, 0, 1);

        $query = substr($q, 1);

        if ($symbol === '@') {
            $results = $this->searchUsername($query);
        } else if ($symbol === '#') {
            $results = $this->searchHashtag($query);
        } else {
            return $this->respondWrongArgs('Query parameter needs to start with symbol @ or #.');
        }

        return $this->respondWithData($results);
    }

    protected function searchUsername($query)
    {
        return User::where('username', 'LIKE', "%$query%")->pluck('username');

    }

    public function searchHashtag($query)
    {
        return Hashtag::where('name', 'LIKE', "%$query%")->pluck('name');
    }
}