<?php

namespace App\Functional\Api\V1\Controllers;

use App\TestCase;

class CommentsControllerTest extends TestCase
{
    protected $path = 'api/comments';

    public function testCommentsDataStructure()
    {
        $res = $this->apiGet([
            'amount'  => 10,
            'page'    => 1,
            'post_id' => 1,
        ]);

        $res->assertSuccessful();

        $res->assertJsonStructure([
            'data' => [
                config('test.json_structure.comment')
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
     * @depends testCreateComment
     *
     * @param $id
     */
    public function testUpdateComment($id)
    {
        $res = $this->apiPatch([
            'body' => 'Test comment body updated',
        ], '/' . $id);

        $res->assertSuccessful();

        return $res->decodeResponseJson()['data']['id'];
    }

    /**
     * @depends testUpdateComment
     *
     * @param $id
     */
    public function testDeleteComment($id)
    {
        $this->apiDelete($id)->assertSuccessful();
    }
}