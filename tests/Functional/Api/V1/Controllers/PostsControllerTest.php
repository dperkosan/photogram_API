<?php

namespace App\Functional\Api\V1\Controllers;

use App\TestCase;

class PostsControllerTest extends TestCase
{
    protected $url = 'api/posts';

    public function queryParams()
    {
        $userId = $this->getTestUserData()['id'];
        $username = $this->getTestUserData()['username'];

        return [
            [
                [
                    'amount' => 10,
                    'page'   => 1,
                ],
            ],
            [
                [
                    'amount'  => 4,
                    'page'    => 1,
                    'user_id' => $userId,
                ],
            ],
            [
                [
                    'amount'   => 8,
                    'page'     => 1,
                    'username' => $username,
                ],
            ],
            [
                [
                    'amount' => 2,
                    'page'   => 2,
                    'news_feed',
                ],
            ],
        ];
    }

    /**
     * @dataProvider queryParams
     *
     * @param $queryParams
     */
    public function testPostsDataStructure($queryParams)
    {
        var_dump($queryParams);
        $res = $this->apiGetWithToken($queryParams);

        $res->assertStatus(200);

        $responseJson = $res->decodeResponseJson();

        $res->assertJsonStructure([
            'data' => [
                config('test.json_structure.post'),
            ],
        ], $responseJson);

        foreach ($responseJson['data'] as $post) {
            if ($post['comments_count'] > 0) {
                $res->assertJsonStructure([
                    config('test.json_structure.comment'),
                ], $post['comments']);
                // to break or not to break
                // that is the question
                break;
            }
        }
    }

    public function testPropertyPageRequired()
    {
        $this->apiGetWithToken([
            'amount' => 10,
        ])->assertStatus(422);
    }

    public function testPropertyAmountRequired()
    {
        $this->apiGetWithToken([
            'page' => 1,
        ])->assertStatus(422);
    }
}