<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 10/11/16
 * Time: 15:17
 */

namespace Solohin\ToptalExam\Tests\API\Users;

class UsersGetTest extends UsersTestTemplate
{
    public function testGetForManager()
    {
        $responseData = $this->makeJsonRequest('/v1/users', 'GET', 'manager', [], true)['users'];

        $this->assertEquals(4, count($responseData));

        $this->assertEquals(1, $responseData[0]['id']);
        $this->assertEquals(2, $responseData[1]['id']);
        $this->assertEquals(3, $responseData[2]['id']);
        $this->assertEquals(4, $responseData[3]['id']);

        $this->assertEquals(true, $responseData[0]['can_edit']);
        $this->assertEquals(false, $responseData[1]['can_edit']);
        $this->assertEquals(true, $responseData[2]['can_edit']);
        $this->assertEquals(true, $responseData[3]['can_edit']);

        //test data structure
        $this->assertArrayHasKey('username', $responseData[0]);
        $this->assertArrayHasKey('can_edit', $responseData[0]);
        $this->assertArrayHasKey('role', $responseData[0]);
        $this->assertArrayHasKey('daily_normal', $responseData[0]);
        $this->assertArrayNotHasKey('token', $responseData[0]);
        $this->assertArrayNotHasKey('password', $responseData[0]);
    }

    public function testPaging()
    {
        $startCount = count($this->makeJsonRequest('/v1/users', 'GET', 'admin', [], true)['users']);
        $toAddCount = 500;
        for ($i = 0; $i < $toAddCount; $i++) {
            $this->usersService->insert([
                'username' => 'username__' . $i,
                'password' => '123' . $i,
            ]);
        }

        $firstPageCount = count($this->makeJsonRequest('/v1/users', 'GET', 'admin', [], true)['users']);
        $secondPageCount = count($this->makeJsonRequest('/v1/users', 'GET', 'admin', ['page' => 2], true)['users']);
        $this->assertEquals($startCount + $toAddCount, $firstPageCount + $secondPageCount);
    }

    public function testGetForAdmin()
    {
        $responseData = $this->makeJsonRequest('/v1/users', 'GET', 'admin', [], true)['users'];

        $this->assertEquals(4, count($responseData));

        $this->assertEquals(1, $responseData[0]['id']);
        $this->assertEquals(2, $responseData[1]['id']);
        $this->assertEquals(3, $responseData[2]['id']);
        $this->assertEquals(4, $responseData[3]['id']);

        $this->assertEquals(true, $responseData[0]['can_edit']);
        $this->assertEquals(true, $responseData[1]['can_edit']);
        $this->assertEquals(true, $responseData[2]['can_edit']);
        $this->assertEquals(true, $responseData[3]['can_edit']);
    }

    public function testGetForUser()
    {
        $responseData = $this->makeJsonRequest('/v1/users', 'GET', 'user', [], false);
        $this->assertEquals('permission_denied', $responseData['error_type']);
    }
}