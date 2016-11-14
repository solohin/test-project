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

class NotesDeleteTest extends NotesTestTemplate
{
    public function testDeleteByUserOwner()
    {
        $startCalories = $this->caloriesSumm500Notes();

        $this->makeJsonRequest('/v1/notes/1', 'DELETE', 'user', [], true);

        $this->assertEquals($startCalories - 100, $this->caloriesSumm500Notes());
    }

    public function testDeleteByUserNotOwner()
    {
        $responseData = $this->makeJsonRequest('/v1/notes/2', 'DELETE', 'user', [], false);
        $this->assertEquals('note_not_found', $responseData['error_type'], print_r($responseData, 1));
    }

    public function testDeleteByManager()
    {
        $responseData = $this->makeJsonRequest('/v1/notes/1', 'DELETE', 'manager', [], false);
        $this->assertEquals('permission_denied', $responseData['error_type'], print_r($responseData, 1));
    }

    public function testDeleteByAdmin()
    {
        $startCalories = $this->caloriesSumm500Notes();

        $this->makeJsonRequest('/v1/notes/1', 'DELETE', 'admin', [], true);
        $this->makeJsonRequest('/v1/notes/2', 'DELETE', 'admin', [], true);

        $this->assertEquals($startCalories - 300, $this->caloriesSumm500Notes());
    }

    public function testDeleteNonexistent()
    {
        $responseData = $this->makeJsonRequest('/v1/notes/9999', 'DELETE', 'admin', [], false);
        $this->assertEquals('note_not_found', $responseData['error_type'], print_r($responseData, 1));
    }
    public function testDeleteWrongId()
    {
        $responseData = $this->makeJsonRequest('/v1/notes/textid', 'DELETE', 'admin', [], false);
        $this->assertEquals('method_not_found', $responseData['error_type'], print_r($responseData, 1));
    }
}