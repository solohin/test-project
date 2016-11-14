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

class NotesUpdateTest extends NotesTestTemplate
{
    public function testUpdateUserOwner()
    {
        $updatedata = [
            'user_id' => 777,
            'time' => '17:35',
            'date' => '19.01.2017',
            'text' => 'Updated note',
            'calories' => 112,
        ];
        $this->makeJsonRequest('/v1/notes/1', 'PUT', 'user', $updatedata, true);

        $updated = $this->makeJsonRequest('/v1/notes/1', 'GET', 'user', [], true)['note'];

        $debug = print_r($updated, 1);
        $this->assertEquals($updatedata['text'], $updated['text'], $debug);
        $this->assertEquals($updatedata['calories'], (int)$updated['calories'], $debug);
        $this->assertEquals(1, (int)$updated['id'], $debug);
        $this->assertEquals($updatedata['date'], $updated['date'], $debug);
        $this->assertEquals($updatedata['time'], $updated['time'], $debug);
    }

    public function testPartialUpdate()
    {
        $oldNote = $this->makeJsonRequest('/v1/notes/2', 'GET', 'admin', [], true)['note'];
        $updatedata = [
            'time' => '19:00',
            'text' => 'Double Updated note',
        ];
        $this->makeJsonRequest('/v1/notes/2', 'PUT', 'admin', $updatedata, true);

        $updated = $this->makeJsonRequest('/v1/notes/2', 'GET', 'admin', [], true)['note'];

        $debug = 'Old: ' . print_r($oldNote, 1) . 'new: ' . print_r($updated, 1) . 'update: ' . print_r($updatedata, 1);
        $this->assertEquals($updatedata['text'], $updated['text'], $debug);
        $this->assertEquals($oldNote['calories'], (int)$updated['calories'], $debug);
        $this->assertEquals($updatedata['time'], $updated['time'], $debug);
        $this->assertEquals($oldNote['user_id'], $updated['user_id'], $debug);
        $this->assertEquals($oldNote['id'], $updated['id'], $debug);
        $this->assertEquals($oldNote['date'], $updated['date'], $debug);
    }

    public function testUpdateUserNotOwner()
    {
        $responseData = $this->makeJsonRequest('/v1/notes/2', 'PUT', 'user', ['text' => '1123'], false);
        $this->assertEquals('note_not_found', $responseData['error_type'], print_r($responseData, 1));
    }

    public function testUpdateAdmin()
    {
        $updatedata = [
            'user_id' => 777,
            'time' => '17:35',
            'date' => '19.01.2017',
            'text' => 'Updated note',
            'calories' => 333,
        ];
        $this->makeJsonRequest('/v1/notes/2', 'PUT', 'admin', $updatedata, true);

        $updated = $this->makeJsonRequest('/v1/notes/2', 'GET', 'admin', [], true)['note'];

        $debug = print_r($updated, 1);
        $this->assertEquals('Updated note', $updated['text'], $debug);
        $this->assertEquals(333, (int)$updated['calories'], $debug);
        $this->assertEquals('17:35', $updated['time'], $debug);
        $this->assertEquals(777, (int)$updated['user_id'], $debug);
        $this->assertEquals('19.01.2017', $updated['date'], $debug);
    }

    public function testUpdateManager()
    {
        $updatedata = [
            'user_id' => 777,
            'time' => '17:35',
            'date' => '19.01.2017',
            'text' => 'Updated note',
            'calories' => 333,
        ];
        $responseData = $this->makeJsonRequest('/v1/notes/1', 'PUT', 'manager', $updatedata, false);
        $this->assertEquals('permission_denied', $responseData['error_type'], print_r($responseData, 1));
    }

    public function testNotChanged()
    {
        $note = $this->makeJsonRequest('/v1/notes/1', 'GET', 'admin', [], true)['note'];
        $this->makeJsonRequest('/v1/notes/1', 'PUT', 'user', ['text' => $note['text']], true);
    }

    public function testUpdateNonexistent()
    {
        $responseData = $this->makeJsonRequest('/v1/notes/999', 'PUT', 'admin', ['text' => '11'], false);
        $this->assertEquals('note_not_found', $responseData['error_type'], print_r($responseData, 1));
    }
    public function testUpdateWrongid()
    {
        $responseData = $this->makeJsonRequest('/v1/notes/wrongid', 'PUT', 'admin', ['text' => '11'], false);
        $this->assertEquals('method_not_found', $responseData['error_type'], print_r($responseData, 1));
    }

    public function testEmptyParams1()
    {
        $responseData = $this->makeJsonRequest('/v1/notes/1', 'PUT', 'admin', [], false);
        $this->assertEquals('empty_parameters', $responseData['error_type'], print_r($responseData, 1));

        $responseData = $this->makeJsonRequest('/v1/notes/1', 'PUT', 'user', ['user_id' => '11'], false);
        $this->assertEquals('empty_parameters', $responseData['error_type'], print_r($responseData, 1));
    }
}