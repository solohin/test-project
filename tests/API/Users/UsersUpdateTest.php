<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 10/11/16
 * Time: 15:06
 */

namespace Solohin\ToptalExam\Tests\API\Users;

class UsersUpdateTest extends UsersTestTemplate
{
    public function testUserRoleChangeForAdmin()
    {
        $this->makeJsonRequest('/v1/users/1', 'PUT', 'admin', ['role' => 'ROLE_ADMIN'], true);
        $user = $this->makeJsonRequest('/v1/users/1', 'GET', 'admin', [], true)['user'];
        $this->assertEquals('ROLE_ADMIN', $user['role']);
    }

    public function testUserWrongRoleChange()
    {
        $this->makeJsonRequest('/v1/users/1', 'PUT', 'admin', ['role' => 'wrong_role'], true);
        $user = $this->makeJsonRequest('/v1/users/1', 'GET', 'admin', [], true)['user'];
        $this->assertEquals('ROLE_USER', $user['role']);
    }

    public function testUserRoleChangeForManager()
    {
        //can not change role to admin
        $response = $this->makeJsonRequest('/v1/users/1', 'PUT', 'manager', ['role' => 'ROLE_ADMIN'], false);
        $this->assertEquals('permission_denied', $response['error_type']);

        //can not change role for admin
        $response = $this->makeJsonRequest('/v1/users/2', 'PUT', 'manager', ['role' => 'ROLE_USER'], false);
        $this->assertEquals('permission_denied', $response['error_type']);

        //can change role to manager
        $this->makeJsonRequest('/v1/users/3', 'PUT', 'manager', ['role' => 'ROLE_USER'], true);
    }

    public function testManagerUpdateAdmin()
    {
        $response = $this->makeJsonRequest('/v1/users/2', 'PUT', 'manager', ['daily_normal' => '1000'], false);
        $this->assertEquals('permission_denied', $response['error_type']);
    }

    public function testUserRoleChangeForUser()
    {
        $response = $this->makeJsonRequest('/v1/users/4', 'PUT', 'user', ['role' => 'ROLE_MANAGER'], false);
        $this->assertEquals('permission_denied', $response['error_type']);

        //role not changed
        $this->makeJsonRequest('/v1/users/1', 'PUT', 'user', ['role' => 'ROLE_MANAGER', 'daily_normal' => 100], true);
        $user = $this->makeJsonRequest('/v1/users/1', 'GET', 'user', [], true)['user'];
        $this->assertArrayHasKey('role', $user, print_r($user, 1));
        $this->assertEquals('ROLE_USER', $user['role'], print_r($user, 1));
        $this->assertEquals(100, (int)$user['daily_normal']);
    }

    public function testUpdateMe()
    {
        //same as testUserRoleChangeForUser

        //role not changed
        $this->makeJsonRequest('/v1/users/me', 'PUT', 'user', ['role' => 'ROLE_MANAGER', 'daily_normal' => 100], true);
        $user = $this->makeJsonRequest('/v1/users/me', 'GET', 'user', [], true)['user'];
        $this->assertEquals('ROLE_USER', $user['role']);
        $this->assertEquals(100, (int)$user['daily_normal']);
    }

    public function testDailyNormalChange()
    {
        //-1
        $this->makeJsonRequest('/v1/users/me', 'PUT', 'user', ['daily_normal' => -1], true);
        $user = $this->makeJsonRequest('/v1/users/me', 'GET', 'user', [], true)['user'];
        $this->assertEquals(2000, (int)$user['daily_normal']);

        //> 32000
        $this->makeJsonRequest('/v1/users/me', 'PUT', 'user', ['daily_normal' => 32001], true);
        $user = $this->makeJsonRequest('/v1/users/me', 'GET', 'user', [], true)['user'];
        $this->assertEquals(2000, (int)$user['daily_normal']);
    }

    public function testIDChange()
    {
        $this->makeJsonRequest('/v1/users/1', 'PUT', 'admin', ['id' => 7], true);
        $response = $this->makeJsonRequest('/v1/users/7', 'GET', 'admin', [], false);
        $this->assertEquals('user_not_found', $response['error_type']);
    }

    public function testUsernameChange()
    {
        $this->makeJsonRequest('/v1/users/2', 'PUT', 'admin', ['username' => 'fff'], true);
        $user2 = $this->makeJsonRequest('/v1/users/2', 'GET', 'admin', [], true)['user'];

        $response = $this->makeJsonRequest('/v1/users/1', 'PUT', 'admin', ['username' => $user2['username']], false);
        $this->assertEquals('username_exists', $response['error_type']);

        $this->makeJsonRequest('/v1/users/1', 'PUT', 'admin', ['username' => 'non existent user name'], true);
        $user = $this->makeJsonRequest('/v1/users/1', 'GET', 'admin', [], true)['user'];
        $this->assertEquals('non existent user name', $user['username']);
    }

    public function testPasswordChange()
    {
        $this->makeJsonRequest('/v1/login', 'POST', 'user', [
            'username' => $this->dummyUsers['user']['username'],
            'password' => $this->dummyUsers['user']['password'],
        ], true);

        $this->makeJsonRequest('/v1/users/me', 'PUT', 'user', ['password' => 123], true);

        //password should not change
        $this->makeJsonRequest('/v1/login', 'POST', 'user', [
            'username' => $this->dummyUsers['user']['username'],
            'password' => $this->dummyUsers['user']['password'],
        ], true);
    }

    public function testTokenChange()
    {
        $this->makeJsonRequest('/v1/users/me', 'PUT', 'user', ['token' => 123], true);
        $this->makeJsonRequest('/v1/users/me', 'GET', 'user', [], true);
    }

    public function testUpdateNonexistent()
    {
        $response = $this->makeJsonRequest('/v1/users/777', 'PUT', 'admin', [], false);
        $this->assertEquals('user_not_found', $response['error_type']);
    }
}