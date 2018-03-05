<?php

namespace App\Functional\Api\V1\Controllers;

use App\TestCase;
use App\DataProvider;

class CommentsControllerTest extends TestCase
{
    protected $url = 'api/comments';

    public function testCommentsDataStructure()
    {
        $token = DataProvider::getToken();
        $res = $this->apiGet($token, [
            'amount'  => 10,
            'page'    => 1,
            'post_id' => 1,
        ]);

        $res->assertStatus(200);

        $res->assertJsonStructure([
            'data',
        ]);
    }
}