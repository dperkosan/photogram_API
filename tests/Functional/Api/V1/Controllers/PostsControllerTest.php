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

    public function testCreateImagePost()
    {
        $res = $this->apiPost([
            'image' => DataProvider::getFakeImage(),
            'description' => 'Test description',
        ]);

        $res->assertSuccessful();

        return $res->decodeResponseJson()['data']['id'];
    }

    /**
     * @depends testCreateImagePost
     *
     * @param $id
     */
    public function testUpdateImagePost($id)
    {
        $res = $this->apiPatch([
            'description' => 'Test description updated',
        ], '/' . $id);

        $res->assertSuccessful();

        return $res->decodeResponseJson()['data']['id'];
    }

    /**
     * @depends testUpdateImagePost
     *
     * @param $id
     */
    public function testDeleteImagePost($id)
    {
        $this->apiDelete($id)->assertSuccessful();
    }
}