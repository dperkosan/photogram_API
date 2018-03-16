<?php

namespace App\Functional\Api\V1\Controllers;

use App\DataProvider;
use App\TestCase;

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

        $res->assertStatus(200);

        $res->assertJsonStructure([
            'data' => [
                $commentDataStructure
            ]
        ]);
    }

    public function testPropertyPageRequired()
    {
        $this->paginationPropertyPageMissing();
    }

    public function testPropertyAmountRequired()
    {
        $this->paginationPropertyAmountMissing();
    }

    public function testCreateComment()
    {
        $res = $this->apiPost([
            'body' => 'Test comment body.',
            'post_id' => 1,
        ]);

        $res->assertSuccessful();

        return $res->decodeResponseJson()['data']['id'];
    }

    /**
     * @depends testCreatePost
     *
     * @param $id
     */
    public function testUpdatePost($id)
    {
        $res = $this->apiPatch($id, [
            'body' => 'Test comment body updated',
        ]);

        $res->assertSuccessful();

        return $res->decodeResponseJson()['data']['id'];
    }

    /**
     * @depends testUpdatePost
     *
     * @param $id
     */
    public function testDeletePost($id)
    {
        $this->apiDelete($id)->assertSuccessful();
    }
}