<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 07/11/16
 * Time: 18:29
 */

namespace Solohin\ToptalExam\Tests\API\Notes;

class NotesAddTest extends NotesTestTemplate
{
    public function testAddByManager()
    {
        $responseData = $this->makeJsonRequest('/v1/notes', 'POST', 'manager', [
            'text' => 'Second note',
            'calories' => 200,
            'user_id' => 2,
            'time' => '14:00',
            'date' => '02.01.2017',
        ], false);

        $debug = 'Response is ' . print_r($responseData, 1);
        $this->assertEquals('permissions_error', $responseData['error_type'], $debug);
    }

    public function testAddByAdmin()
    {
        $startCalories = $this->caloriesSumm500Notes();
        $this->makeJsonRequest('/v1/notes', 'POST', 'admin', [
            'text' => 'Second note',
            'calories' => 200,
            'user_id' => 2,
            'time' => '14:00',
            'date' => '02.01.2017',
        ], true);
        $this->assertEquals($startCalories + 200, $this->caloriesSumm500Notes());
    }

    public function testAddByAdminWithoutUser()
    {
        $responseData = $this->makeJsonRequest('/v1/notes', 'POST', 'admin', [
            'text' => 'Second note',
            'calories' => 200,
            'time' => '14:00',
            'date' => '02.01.2017',
        ], false);
        $this->assertEquals('empty_user_id', $responseData['error_type'], print_r($responseData, 1));
    }

    public function testAddByUserOwner()
    {
        $startCalories = $this->caloriesSumm500Notes();
        $responseData = $this->makeJsonRequest('/v1/notes', 'POST', 'admin', [
            'text' => 'Second note',
            'calories' => 400,
            'time' => '14:00',
            'date' => '02.01.2017',
            'user_id' => $this->dummyUsers['user']['token'],
        ], true);
        $this->assertEquals($startCalories + 400, $this->caloriesSumm500Notes());

    }

    public function testAddByUserNotOwner()
    {
        $userCalories = $this->caloriesSumm500Notes('user');

        $this->makeJsonRequest('/v1/notes', 'POST', 'admin', [
            'text' => 'Second note',
            'calories' => 400,
            'time' => '14:00',
            'date' => '02.01.2017',
            'user_id' => $this->dummyUsers['admin']['token'],
        ], true);

        //It should be added to user's account without error
        $this->assertEquals($userCalories + 400, $this->caloriesSumm500Notes('user'));
    }

    public function testAddWrongDate()
    {
        $responseData = $this->makeJsonRequest('/v1/notes', 'POST', 'manager', [
            'text' => 'Second note',
            'calories' => 200,
            'user_id' => 2,
            'time' => '14:00',
            'date' => '02-01-2017',
        ], false);

        $debug = 'Response is ' . print_r($responseData, 1);
        $this->assertEquals('wrong_date', $responseData['error_type'], $debug);
    }

    public function testAddWrongTime()
    {
        $responseData = $this->makeJsonRequest('/v1/notes', 'POST', 'manager', [
            'text' => 'Second note',
            'calories' => 200,
            'user_id' => 2,
            'time' => 'twenty four sixty one',
            'date' => '02.01.2017',
        ], false);

        $debug = 'Response is ' . print_r($responseData, 1);
        $this->assertEquals('wrong_time', $responseData['error_type'], $debug);
    }
}