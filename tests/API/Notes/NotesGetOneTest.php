<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 07/11/16
 * Time: 18:29
 */

namespace Solohin\ToptalExam\Tests\API\Notes;

use Silex\WebTestCase;
use Solohin\ToptalExam\Services\UsersService;

class NotesGetOneTest extends NotesTestTemplate
{
    public function testGetOneByUserOwner()
    {
        $responseData = $this->makeJsonRequest('/v1/notes/1', 'get', 'user', [], true);

        $debug = print_r($responseData, 1);

        $this->assertEquals('First note', $responseData['note']['text'], $debug);
        $this->assertEquals(100, (int)$responseData['note']['calories'], $debug);
        $this->assertEquals('12:00', $responseData['note']['time'], $debug);
        $this->assertEquals(1, (int)$responseData['note']['id'], $debug);
        $this->assertEquals('01.01.2017', $responseData['note']['date'], $debug);
    }

    public function testGetOneByUserNotOwner()
    {
        $responseData = $this->makeJsonRequest('/v1/notes/2', 'GET', 'user', [], false);
        $this->assertEquals('note_not_found', $responseData['error_type'], print_r($responseData, 1));
    }

    public function testGetOneByAdmin()
    {
        $this->makeJsonRequest('/v1/notes/1', 'get', 'admin', [], true);
        $this->makeJsonRequest('/v1/notes/2', 'get', 'admin', [], true);
    }

    public function testGetOneByManager()
    {
        $responseData = $this->makeJsonRequest('/v1/notes/1', 'get', 'manager', [], false);
        $this->assertEquals('permission_denied', $responseData['error_type'], print_r($responseData, 1));
    }
}