<?php

namespace App\Functional\Api\V1\Controllers;

use App\TestCase;
use App\DataProvider;

class CommentsControllerTest extends TestCase
{
    protected $path = 'api/comments';

    public function testCommentsDataStructure()
    {
        $commentDataStructure = config('test.json_structure.comment');

        $res = $this->apiGet([
            'amount'  => 10,
            'page'    => 1,
            'post_id' => 1,
        ]);
echo $res->getContent();
        $res->assertStatus(200);

        $res->assertJsonStructure([
            'data' => [
                $commentDataStructure
            ]
        ]);
    }
}