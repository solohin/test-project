<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 10/11/16
 * Time: 15:06
 */

namespace Solohin\ToptalExam\Tests\API\Users;

class UsersAddTest extends UsersTestTemplate
{
    public function testAddForAdmin()
    {
        $inserted = $this->makeJsonRequest('/v1/users', 'POST', 'admin', [
            'username' => 'test 1',
        ], false);
        $this->assertEquals('method_not_found', $inserted['error_type']);
    }

    public function testAddForManager()
    {
        $inserted = $this->makeJsonRequest('/v1/users', 'POST', 'manager', [
            'username' => 'test 1',
        ], false);
        $this->assertEquals('method_not_found', $inserted['error_type']);
    }

    public function testAddForUser()
    {
        $inserted = $this->makeJsonRequest('/v1/users', 'POST', 'user', [
            'username' => 'test 1',
        ], false);
        $this->assertEquals('method_not_found', $inserted['error_type']);
    }
}