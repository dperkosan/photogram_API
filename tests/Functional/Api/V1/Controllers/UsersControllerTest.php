<?php

namespace App\Functional\Api\V1\Controllers;

use App\DataProvider;
use App\TestCase;

class UsersControllerTest extends TestCase
{
    protected $path = 'api/users';

    /**
     * @group current
     */
    public function testUpdateAuthUser()
    {
        $res = $this->patch($this->path . '/auth/update', [
            'username'  => 'updated.username',
            'name'      => 'updated name',
            'gender_id' => 2,
            'phone'     => '987654321',
            'about'     => 'Updated about field',
        ], DataProvider::getHeader());

        $res->assertSuccessful();

        // Refresh the test user

        DataProvider::unsetKey('test_user');

        DataProvider::getTestUser()->update(array_pluck(DataProvider::getTestUserData(), [
            'username', 'name', 'gender_id', 'phone', 'about',
        ]));
    }

    /**
     * @group current
     */
    public function testGetAuthUserDataStructure()
    {
        $this->apiGet([], '/auth')->assertJsonStructure([
            'data' => config('test.json_structure.auth_user'),
        ]);
    }

    /**
     * @group current
     */
    public function testExists()
    {
        $res = $this->apiGet(array_pluck(DataProvider::getTestUserData(), [
            'username', 'email', 'name', 'gender_id'
        ]), '/exists');

        $res->assertSuccessful();

        $responseData = $res->decodeResponseJson();

        $res->assertJsonStructure([
            'data' => [
                'exists',
            ],
        ], $responseData);

        $this->assertEquals(true, $responseData['data']['exists']);
    }

    /**
     * @group current
     */
    public function testFind()
    {
        $data = array_pluck(DataProvider::getTestUserData(), [
            'username', 'email', 'name', 'gender_id'
        ]);

        $this->apiGet($data, '/find')->assertSuccessful()->assertJsonStructure([
            'data' => config('test.json_structure.user'),
        ]);
    }

    /**
     * @group current
     */
    public function testUpdateAuthProfileImage()
    {
        $this->apiPost([
            'image' => DataProvider::getFakeImage(),
        ], '/auth/image')->assertSuccessful()->assertJsonStructure([
            'data' => [
                'image' => config('test.json_structure.user_image'),
            ]
        ]);
    }
}