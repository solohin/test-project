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
use Symfony\Component\Debug\Debug;

class NotesGetTest extends NotesTestTemplate
{
    public function testGetAllAdmin()
    {
        $this->assertEquals(3100, $this->caloriesSumm500Notes());
    }

    public function testEmptyParams()
    {
        $totalCalories = $this->makeJsonRequest('/v1/notes', 'GET', 'user', [
            'to_time' => '',
            'from_time' => '',
            'to_date' => '',
            'from_date' => '',
        ], true)['total_calories'];
        $this->assertEquals(2900, $totalCalories);
    }

    public function testSort()
    {
        $notes = $this->makeJsonRequest('/v1/notes', 'GET', 'admin', [], true)['notes'];
        $lastVal = PHP_INT_MAX;
        foreach ($notes as $note) {
            $noteDate = \DateTime::createFromFormat('d.m.Y H:i:s', $note['date'] . ' 00:00:00')->getTimestamp();

            $tempTime = explode(':', $note['time']);
            $hours = intval($tempTime[0]);
            $minutes = intval($tempTime[1]);
            $noteTime = $hours * 60 * 60 + $minutes * 60;

            $this->assertLessThan($lastVal, $noteDate + $noteTime);
            $lastVal = $noteDate + $noteTime;
        }
    }

    public function testTotalCalories()
    {
        $totalCalories = $this->makeJsonRequest('/v1/notes', 'GET', 'user', [], true)['total_calories'];
        $this->assertEquals(2900, $totalCalories);
    }

    public function testHasUserName()
    {
        $notes = $this->makeJsonRequest('/v1/notes', 'GET', 'admin', [], true)['notes'];
        $this->assertArrayHasKey('username', $notes[0]);
        $this->assertEquals('dummyUser', $notes[0]['username']);
        $this->assertEquals('dummyUser2', $notes[2]['username'], print_r($notes, 1));
    }

    public function testOrder()
    {
        $notes = $this->makeJsonRequest('/v1/notes', 'GET', 'user', [], true)['notes'];
        $this->assertTrue($notes[0]['id'] > $notes[count($notes) - 1]['id']);
    }

    public function testDailyNormal()
    {
        $notes = $this->get500Notes();
        $result = array_column($notes, 'daily_normal', 'id');

        $this->assertEquals(true, $result[1]);
        $this->assertEquals(false, $result[2]);
        $this->assertEquals(true, $result[3]);
        $this->assertEquals(true, $result[4]);
        $this->assertEquals(false, $result[5]);
        $this->assertEquals(false, $result[6]);
    }

    public function testGetAllUser()
    {
        $this->assertEquals(2900, $this->caloriesSumm500Notes('user'));
    }

    public function testGetAllManager()
    {
        $responseData = $this->makeJsonRequest('/v1/notes', 'GET', 'manager', [], false);
        $this->assertEquals('permission_denied', $responseData['error_type']);
    }

    public function testGetAllPaging()
    {
        $onPage = 129;
        $startCount = count($this->makeJsonRequest('/v1/notes', 'GET', 'admin', [], true)['notes']);
        for ($i = 0; $i < $onPage; $i++) {
            $this->notesService->insert([
                'text' => 'Some note',
                'calories' => 100,
                'user_id' => 1,
                'time' => '12:00',
                'date' => '01.01.2017',
            ]);
        }

        $firstPage = $this->makeJsonRequest('/v1/notes', 'GET', 'admin', ['on_page' => $onPage], true);
        $secondPage = $this->makeJsonRequest('/v1/notes', 'GET', 'admin', ['page' => 2, 'on_page' => $onPage], true);

        $firstPageCount = count($firstPage['notes']);
        $secondPageCount = count($secondPage['notes']);
        $this->assertEquals($startCount + $onPage, $firstPageCount + $secondPageCount);


        $this->assertArrayHasKey('has_more_pages', $firstPage);
        $this->assertTrue($firstPage['has_more_pages']);

        $this->assertArrayHasKey('has_more_pages', $secondPage);
        $this->assertFalse($secondPage['has_more_pages']);
    }

    public function testGetAllAdminDateFilter()
    {
        $notes = $this->makeJsonRequest('/v1/notes', 'GET', 'admin', ['to_date' => '02.01.2017'], true)['notes'];
        $calories = array_sum(array_column($notes, 'calories'));
        $this->assertEquals(1900, $calories);

        $notes = $this->makeJsonRequest('/v1/notes', 'GET', 'admin', ['from_date' => '02.01.2017'], true)['notes'];
        $calories = array_sum(array_column($notes, 'calories'));
        $this->assertEquals(1800, $calories);

        $notes = $this->makeJsonRequest('/v1/notes', 'GET', 'admin', [
            'to_date' => '02.01.2017',
            'from_date' => '02.01.2017',
        ], true)['notes'];
        $calories = array_sum(array_column($notes, 'calories'));
        $this->assertEquals(600, $calories);
    }

    public function testGetAllAdminTimeFilter()
    {
        $notes = $this->makeJsonRequest('/v1/notes', 'GET', 'admin', ['from_time' => '17:40'], true)['notes'];
        $calories = array_sum(array_column($notes, 'calories'));
        $this->assertEquals(1200, $calories);

        $notes = $this->makeJsonRequest('/v1/notes', 'GET', 'admin', ['to_time' => '17:40'], true)['notes'];
        $calories = array_sum(array_column($notes, 'calories'));
        $this->assertEquals(2200, $calories);

        $notes = $this->makeJsonRequest('/v1/notes', 'GET', 'admin', [
            'to_time' => '19:00',
            'from_time' => '14:00',
        ], true)['notes'];
        $calories = array_sum(array_column($notes, 'calories'));
        $this->assertEquals(900, $calories);
    }

    public function testGetAllAdminUserFilter()
    {
        $notes = $this->makeJsonRequest('/v1/notes', 'GET', 'admin', ['user_id' => '1'], true)['notes'];
        $calories = array_sum(array_column($notes, 'calories'));
        $this->assertEquals(2900, $calories);
    }

    public function testGetAllUsernotOwnerFilter()
    {
        $notes = $this->makeJsonRequest('/v1/notes', 'GET', 'user', ['user_id' => '4'], true)['notes'];
        $calories = array_sum(array_column($notes, 'calories'));
        $this->assertEquals(2900, $calories);//user_id == 1
    }

    public function testGetAllUserDateTimeFilter()
    {
        $notes = $this->makeJsonRequest('/v1/notes', 'GET', 'user', [
            'to_time' => '23:59',
            'from_time' => '14:00',
            'to_date' => '03.01.2017',
            'from_date' => '02.01.2017',
        ], true)['notes'];
        $calories = array_sum(array_column($notes, 'calories'));
        $this->assertEquals(400, $calories);
    }

    public function testWrongDate()
    {
        $this->makeJsonRequest('/v1/notes', 'GET', 'user', [
            'from_date' => '02-01-2017',
        ], false);
    }

    public function testWrongTime()
    {
        $this->makeJsonRequest('/v1/notes', 'GET', 'user', [
            'from_time' => '02-17',
        ], false);
    }
}