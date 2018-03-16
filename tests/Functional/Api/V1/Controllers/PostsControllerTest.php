<?php

namespace App\Functional\Api\V1\Controllers;

use App\DataProvider;
use App\Post;
use App\TestCase;
use Illuminate\Http\UploadedFile;

class PostsControllerTest extends TestCase
{
    protected $path = 'api/posts';

    public function queryParams()
    {
        // Refresh (or create) application needs to happen here because
        // data providers are loaded before the setUp() by phpunit
        // and laravel app is loaded only in setUp()
        $this->refreshApplicationIfNotRefreshed();
        $user = DataProvider::getTestUser();
        $userId = $user->id;
        $username = $user->username;

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
        $postDataStructure = config('test.json_structure.post');
        $commentDataStructure = config('test.json_structure.comment');

        $res = $this->apiGet($queryParams);

        $res->assertStatus(200);

        $responseJson = $res->decodeResponseJson();

        $res->assertJsonStructure([
            'data' => [
                $postDataStructure,
            ],
        ], $responseJson);

        foreach ($responseJson['data'] as $post) {
            if ($post['comments_count'] > 0) {
                $res->assertJsonStructure([
                    $commentDataStructure,
                ], $post['comments']);
                // to break or not to break
                // that is the question
                break;
            }
        }
    }

    public function testPropertyPageRequired()
    {
        $this->paginationPropertyPageMissing();
    }

    public function testPropertyAmountRequired()
    {
        $this->paginationPropertyAmountMissing();
    }

    public function testCreatePost()
    {
        $res = $this->apiPost([
            'image' => DataProvider::getFakeImage(),
            'thumbnail' => DataProvider::getFakeImage(),
            'description' => 'Test description',
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
//            'thumbnail' => DataProvider::getFakeImage(),
            'description' => 'Test description updated',
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