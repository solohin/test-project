<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 10/11/16
 * Time: 15:13
 */

namespace Solohin\ToptalExam\Tests\API\Users;

class UsersDeleteTest extends UsersTestTemplate
{
    public function testDeleteForAdmin()
    {
        //delete all except 2 (2 is admin)

        $this->makeJsonRequest('/v1/users/1', 'GET', 'admin', [], true);
        $this->makeJsonRequest('/v1/users/3', 'GET', 'admin', [], true);
        $this->makeJsonRequest('/v1/users/4', 'GET', 'admin', [], true);

        $this->makeJsonRequest('/v1/users/1', 'DELETE', 'admin', [], true);
        $this->makeJsonRequest('/v1/users/3', 'DELETE', 'admin', [], true);
        $this->makeJsonRequest('/v1/users/4', 'DELETE', 'admin', [], true);
        $this->makeJsonRequest('/v1/users/5', 'DELETE', 'admin', [], false);

        $this->makeJsonRequest('/v1/users/1', 'GET', 'admin', [], false);
        $this->makeJsonRequest('/v1/users/2', 'GET', 'admin', [], true);
        $this->makeJsonRequest('/v1/users/3', 'GET', 'admin', [], false);
        $this->makeJsonRequest('/v1/users/4', 'GET', 'admin', [], false);
    }

    public function testDeleteForManager()
    {
        //can not delete #2 - its admin

        $this->makeJsonRequest('/v1/users/1', 'GET', 'manager', [], true);
        $this->makeJsonRequest('/v1/users/2', 'GET', 'manager', [], true);
        $this->makeJsonRequest('/v1/users/4', 'GET', 'manager', [], true);

        $this->makeJsonRequest('/v1/users/1', 'DELETE', 'manager', [], true);
        $this->makeJsonRequest('/v1/users/2', 'DELETE', 'manager', [], false);
        $this->makeJsonRequest('/v1/users/4', 'DELETE', 'manager', [], true);
        $this->makeJsonRequest('/v1/users/5', 'DELETE', 'manager', [], false);

        $this->makeJsonRequest('/v1/users/1', 'GET', 'manager', [], false);
        $this->makeJsonRequest('/v1/users/2', 'GET', 'manager', [], true);
        $this->makeJsonRequest('/v1/users/4', 'GET', 'manager', [], false);
    }

    public function testDeleteForUser()
    {
        //con not delete
        $response1 = $this->makeJsonRequest('/v1/users/4', 'DELETE', 'user', [], false);
        $response2 = $this->makeJsonRequest('/v1/users/4', 'DELETE', 'user', [], false);
        $response3 = $this->makeJsonRequest('/v1/users/4', 'DELETE', 'user', [], false);
        $response4 = $this->makeJsonRequest('/v1/users/4', 'DELETE', 'user', [], false);

        $this->assertEquals('permission_denied', $response1['error_type']);
        $this->assertEquals('permission_denied', $response2['error_type']);
        $this->assertEquals('permission_denied', $response3['error_type']);
        $this->assertEquals('permission_denied', $response4['error_type']);
    }

    public function testDeleteNonexistent()
    {
        $response = $this->makeJsonRequest('/v1/users/9999', 'DELETE', 'admin', [], false);
        $this->assertEquals('user_not_found', $response['error_type']);
    }

    public function testDeleteMyself()
    {
        //now I am alive
        $this->makeJsonRequest('/v1/users', 'GET', 'admin', [], true);

        //Delete myself
        $this->makeJsonRequest('/v1/users/3', 'GET', 'admin', [], true);

        //I have auth error
        $response = $this->makeJsonRequest('/v1/users', 'GET', 'admin', [], false);
        $this->assertEquals('wrong_token', $response['token']);
    }
}