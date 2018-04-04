<?php

namespace App\Functional\Api\V1\Controllers;

use App\DataProvider;
use App\TestCase;

class CommentsControllerTest extends TestCase
{
    protected $path = 'api/comments';

    /**
     * @group single
     */
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
        $pathSuffix = '/' . $id;

        $res = $this->apiPatch([
            'body' => 'Test comment body updated',
            'reply_username' => DataProvider::getTestUser()->username,
        ], $pathSuffix);

        $res->assertSuccessful();

        $res = $this->apiPatch([
            'body' => 'Test comment body updated again',
            'reply_user_id' => DataProvider::getTestUser()->id,
        ], $pathSuffix);

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