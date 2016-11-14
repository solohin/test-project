<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 10/11/16
 * Time: 15:17
 */

namespace Solohin\ToptalExam\Tests\API\Users;

class UsersGetOneTest extends UsersTestTemplate
{
    public function testGetOneForManager()
    {
        $responseDataAdmin = $this->makeJsonRequest('/v1/users/2', 'get', 'manager', [], true)['user'];
        $responseDataManager = $this->makeJsonRequest('/v1/users/3', 'get', 'manager', [], true)['user'];

        $this->assertEquals('dummyAdmin', $responseDataAdmin['username']);
        $this->assertEquals('ROLE_ADMIN', $responseDataAdmin['role']);
        $this->assertEquals(400, $responseDataAdmin['daily_normal']);
        $this->assertEquals(false, $responseDataAdmin['can_edit']);
        $this->assertArrayNotHasKey('token', $responseDataAdmin);
        $this->assertArrayNotHasKey('password', $responseDataAdmin);
        $this->assertArrayNotHasKey('roles', $responseDataAdmin);

        $this->assertEquals('dummyManager', $responseDataManager['username']);
        $this->assertEquals('ROLE_MANAGER', $responseDataManager['role']);
        $this->assertEquals(400, $responseDataManager['daily_normal']);
        $this->assertEquals(true, $responseDataManager['can_edit']);
        $this->assertArrayNotHasKey('token', $responseDataAdmin);
        $this->assertArrayNotHasKey('password', $responseDataAdmin);
    }

    public function testGetOneForAdmin()
    {
        $responseDataAdmin = $this->makeJsonRequest('/v1/users/2', 'get', 'admin', [], true)['user'];
        $responseDataManager = $this->makeJsonRequest('/v1/users/3', 'get', 'admin', [], true)['user'];

        $this->assertEquals('dummyAdmin', $responseDataAdmin['username']);
        $this->assertEquals('ROLE_ADMIN', $responseDataAdmin['role']);
        $this->assertEquals(400, $responseDataAdmin['daily_normal']);
        $this->assertEquals(true, $responseDataAdmin['can_edit']);
        $this->assertArrayNotHasKey('token', $responseDataAdmin);
        $this->assertArrayNotHasKey('password', $responseDataAdmin);

        $this->assertEquals('dummyManager', $responseDataManager['username']);
        $this->assertEquals('ROLE_MANAGER', $responseDataManager['role']);
        $this->assertEquals(400, $responseDataManager['daily_normal']);
        $this->assertEquals(true, $responseDataManager['can_edit']);
        $this->assertArrayNotHasKey('token', $responseDataAdmin);
        $this->assertArrayNotHasKey('password', $responseDataAdmin);
    }

    public function testGetOneForUserNotOwner()
    {
        $responseData = $this->makeJsonRequest('/v1/users/3', 'get', 'user', [], false);
        $this->assertEquals('permission_denied', $responseData['error_type']);
    }
    public function testGetOneWrongId()
    {
        $responseData = $this->makeJsonRequest('/v1/users/WrongId', 'get', 'user', [], false);
        $this->assertEquals('method_not_found', $responseData['error_type']);
    }

    public function testGetOneForUserOwner()
    {
        $responseData1 = $this->makeJsonRequest('/v1/users/1', 'get', 'user', [], true)['user'];
        $responseDataMe = $this->makeJsonRequest('/v1/users/me', 'get', 'user', [], true)['user'];
        $this->assertEquals($responseData1, $responseDataMe);

        $this->assertArrayNotHasKey('token', $responseData1);
        $this->assertArrayNotHasKey('password', $responseData1);
        $this->assertEquals(true, $responseData1['can_edit']);
    }
}