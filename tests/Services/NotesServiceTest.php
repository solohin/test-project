<?php

namespace Solohin\ToptalExam\Tests\Services;

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Solohin\ToptalExam\Database\SchemaManager;
use Solohin\ToptalExam\Services\NotesService;
use Solohin\ToptalExam\Services\UsersService;


class NotesServiceTest extends \PHPUnit_Framework_TestCase
{
    const ON_PAGE = 500;
    /** @var $notesService NotesService */
    private $notesService;

    public function setUp()
    {
        $app = new Application();
        $app->register(new DoctrineServiceProvider(), \Solohin\ToptalExam\Tests\Utils\DataBase::getTestDBParams());

        $schemaManager = new SchemaManager($app['db']);
        $schemaManager->flushDatabase();


        //Add notes
        $this->notesService = new NotesService($app['db']);

        /*
         * 100 - 1 - 12:00 - 01.01.2017
         * 200 - 2 - 14:00 - 02.01.2017
         * 300 - 1 - 17:40 - 01.01.2017
         * 400 - 1 - 16:05 - 02.01.2017
         * 900 - 1 - 23:59 - 01.01.2017
         * 1200 - 1 - 00:00 - 03.01.2017
         */

        $this->notesService->insert([
            'text' => 'First note',
            'calories' => 100,
            'user_id' => 1,
            'time' => '12:00',
            'date' => '01.01.2017',
        ]);

        $this->notesService->insert([
            'text' => 'Second note',
            'calories' => 200,
            'user_id' => 2,
            'time' => '14:00',
            'date' => '02.01.2017',
        ]);

        $this->notesService->insert([
            'text' => 'Third note',
            'calories' => 300,
            'user_id' => 1,
            'time' => '17:40',
            'date' => '01.01.2017',
        ]);

        $this->notesService->insert([
            'text' => 'Fourth note',
            'calories' => 400,
            'user_id' => 1,
            'time' => '16:05',
            'date' => '02.01.2017',
        ]);
        $this->notesService->insert([
            'text' => 'Fifth note',
            'calories' => 900,
            'user_id' => 1,
            'time' => '23:59',
            'date' => '01.01.2017',
        ]);
        $this->notesService->insert([
            'text' => 'Sixth note',
            'calories' => 1200,
            'user_id' => 1,
            'time' => '00:00',
            'date' => '03.01.2017',
        ]);

        $usersService = new UsersService($app['db']);
    }

    private function assertArrayIsPartOfArray($expected, $actual)
    {
        $this->assertTrue(is_array($actual), 'Actual is not an array ' . print_r($actual, 1));
        $this->assertTrue(is_array($expected), 'Expected is not an array ' . print_r($expected, 1));

        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $actual, 'No key ' . $key . ', keys: ' . implode(', ', array_keys($actual)));
            $this->assertEquals($value, $actual[$key], 'Wrong key' . $key . ' value');
        }
    }

    public function testGetOne()
    {
        $note = $this->notesService->getOne(2);

        $this->assertArrayIsPartOfArray([
            'text' => 'Second note',
            'calories' => 200,
            'user_id' => 2,
            'time' => '14:00',
            'date' => '02.01.2017',
        ], $note);
    }

    public function testGetOneWithRightUserID()
    {
        $note = $this->notesService->getOne(2, 2);

        $this->assertArrayIsPartOfArray([
            'text' => 'Second note',
            'calories' => 200,
            'user_id' => 2,
            'time' => '14:00',
            'date' => '02.01.2017',
        ], $note);
    }

    public function testGetOneWithWrongUserID()
    {
        $note = $this->notesService->getOne(2, 1);

        $this->assertFalse($note);
    }

    public function testGetOneNonexistent()
    {
        $note = $this->notesService->getOne(99999);

        $this->assertFalse($note);
    }

    public function testGetAll()
    {
        $notes = $this->notesService->getAll();
        $this->assertCount(6, $notes);
        $this->assertEquals(3100, array_sum(array_column($notes, 'calories')));
    }

    public function testGetAllWithUserID()
    {
        $notes = $this->notesService->getAll(1);
        $this->assertCount(5, $notes);
        $this->assertEquals(2900, array_sum(array_column($notes, 'calories')));

        $notes = $this->notesService->getAll(2);
        $this->assertCount(1, $notes);
        $this->assertEquals(200, array_sum(array_column($notes, 'calories')));
    }

    /*
     * kCal User    Time    Date
     * 100  1       12:00   01.01.2017
     * 200  2       14:00   02.01.2017
     * 300  1       17:40   01.01.2017
     * 400  1       16:05   02.01.2017
     * 900  1       23:59   01.01.2017
     * 1200 1       00:00   03.01.2017
     */

    public function testGetAllWithDates()
    {
        $notes = $this->notesService->getAll(
            $userIdFilter = null,
            '02.01.2017',
            $toDate = null,
            $fromTime = null,
            $toTime = null
        );
        $this->assertEquals(1800, array_sum(array_column($notes, 'calories')));

        $notes = $this->notesService->getAll(
            $userIdFilter = null,
            $fromDate = null,
            '02.01.2017',
            $fromTime = null,
            $toTime = null
        );
        $this->assertEquals(1900, array_sum(array_column($notes, 'calories')));

        $notes = $this->notesService->getAll(
            $userIdFilter = null,
            '02.01.2017',
            '02.01.2017',
            $fromTime = null,
            $toTime = null
        );
        $this->assertEquals(600, array_sum(array_column($notes, 'calories')));
    }

    /**
     * @expectedException \Exception
     */
    public function testGetAllWithWrongDates()
    {
        $notes = $this->notesService->getAll(
            $userIdFilter = null,
            '31/12/2017',
            $toDate = null,
            $fromTime = null,
            $toTime = null
        );
    }

    public function testGetAllWithTime()
    {
        $notes = $this->notesService->getAll(
            $userIdFilter = null,
            $fromDate = null,
            $toDate = null,
            $fromTime = '17:40',
            $toTime = null
        );
        $this->assertEquals(1200, array_sum(array_column($notes, 'calories')));

        $notes = $this->notesService->getAll(
            $userIdFilter = null,
            $fromDate = null,
            $toDate = null,
            $fromTime = null,
            $toTime = '17:40'
        );
        $this->assertEquals(2200, array_sum(array_column($notes, 'calories')));

        $notes = $this->notesService->getAll(
            $userIdFilter = null,
            $fromDate = null,
            $toDate = null,
            $fromTime = '14:00',
            $toTime = '19:00'
        );
        $this->assertEquals(900, array_sum(array_column($notes, 'calories')));
    }

    /**
     * @expectedException \Exception
     */
    public function testGetAllWithWrongTime1()
    {
        $notes = $this->notesService->getAll(
            $userIdFilter = null,
            $fromDate = null,
            $toDate = null,
            '',
            $toTime = null
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testGetAllWithWrongTime2()
    {
        $notes = $this->notesService->getAll(
            $userIdFilter = null,
            $page = 1,
            $fromDate = null,
            $toDate = null,
            '24:61',
            $toTime = null
        );
    }

    public function testGetAllFullFiltered()
    {
        $notes = $this->notesService->getAll(
            $userIdFilter = 1,
            $fromDate = '02.01.2017',
            $toDate = '03.01.2017',
            $fromTime = '14:00',
            $toTime = '23:59'
        );
        $this->assertEquals(400, array_sum(array_column($notes, 'calories')));
    }

    public function testGetAllPaging()
    {
        $startCount = count($this->notesService->getAll());
        for ($i = 0; $i < self::ON_PAGE; $i++) {
            $this->notesService->insert([
                'text' => 'Note #' . $i,
                'calories' => 100,
                'user_id' => 1,
                'time' => '23:59',
                'date' => '01.01.2017',
            ]);
        }
        $firstPage = $this->notesService->getAll();

        $this->assertCount(self::ON_PAGE, $firstPage);

        $secondPage = $this->notesService->getAll(null, null, null, null, null, $page = 2);

        //Real pages
        $this->assertCount($startCount, $secondPage);

        $thirdPage = $this->notesService->getAll(null, null, null, null, null, $page = 3);

        //Fake page
        $this->assertCount(0, $thirdPage);

        $zeroPage = $this->notesService->getAll(null, null, null, null, null, $page = 0);
        $minusOnePage = $this->notesService->getAll(null, null, null, null, null, $page = -1);

        //And wrong pages is first page
        $this->assertEquals($firstPage, $zeroPage);
        $this->assertEquals($firstPage, $minusOnePage);
    }

    public function testInsert()
    {
        $startCount = count($this->notesService->getAll());

        $id = $this->notesService->insert([
            'text' => 'Note #zero',
            'calories' => 100,
            'user_id' => 1,
            'time' => '23:59',
            'date' => '01.01.2017',
        ]);

        $this->assertEquals('Note #zero', $this->notesService->getOne($id)['text']);

        $allNotes = $this->notesService->getAll();
        $this->assertCount($startCount + 1, $allNotes);
    }

    public function testInsertLongText()
    {
        $startCount = count($this->notesService->getAll());

        $longText = str_repeat('hello привет Ֆիններեն', 10000);

        $id = $this->notesService->insert([
            'text' => $longText,
            'calories' => 100,
            'user_id' => 1,
            'time' => '23:59',
            'date' => '01.01.2017',
        ]);

        //Very long text is not accepted
        $this->assertNotEquals('Note #zero', $this->notesService->getOne($id)['text']);

        $allNotes = $this->notesService->getAll();
        $this->assertCount($startCount + 1, $allNotes);
    }

    /**
     * @expectedException \Exception
     */
    public function testInsertWrongTime()
    {
        $this->notesService->insert([
            'text' => 'Note #zero',
            'calories' => 100,
            'user_id' => 1,
            'time' => '24:59',
            'date' => '01.01.2017',
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function testInsertWrongDate()
    {
        $this->notesService->insert([
            'text' => 'Note #zero',
            'calories' => 100,
            'user_id' => 1,
            'time' => '23:59',
            'date' => '32-01-2017',
        ]);
    }

    public function testUpdate()
    {
        $id = 1;
        $note = $this->notesService->getOne($id);
        $note['calories'] = 99999;
        $this->notesService->update($id, $note);

        $newNote = $this->notesService->getOne($id);

        $this->assertArrayIsPartOfArray($note, $newNote);
    }

    public function testUpdateWithUserID()
    {
        //for owner
        $id = 1;
        $userid = 1;

        $note = $this->notesService->getOne($id);
        $note['calories'] = 99999;
        $updated = $this->notesService->update($id, $note, $userid);
        $this->assertTrue($updated);
        $newNote = $this->notesService->getOne($id);
        $this->assertArrayIsPartOfArray($note, $newNote);
        $this->assertEquals($note['calories'], $newNote['calories']);

        //not for owner
        $id = 2;
        $userid = 1;

        $note = $this->notesService->getOne($id);
        $note['calories'] = 99999;
        $updated = $this->notesService->update($id, $note, $userid);
        $this->assertFalse($updated);
        $newNote = $this->notesService->getOne($id);
        $this->assertNotEquals($note['calories'], $newNote['calories']);

    }

    public function testUpdateNonexistent()
    {
        $id = 9999999;
        $userid = 1;

        $note = $this->notesService->getOne($id);
        $note['calories'] = 99999;
        $updated = $this->notesService->update($id, $note, $userid);
        $this->assertFalse($updated);
        $newNote = $this->notesService->getOne($id);
        $this->assertFalse($newNote);
    }

    public function testDelete()
    {
        $id = 4;
        $note = $this->notesService->getOne($id);
        $this->assertArrayHasKey('id', $note);

        //then delete
        $success = $this->notesService->delete($id);

        $newNote = $this->notesService->getOne($id);
        $this->assertFalse($newNote);
    }

    public function testDeleteWithUserID()
    {
        //for owner
        $id = 1;
        $userid = 1;

        $note = $this->notesService->getOne($id);
        $this->assertArrayHasKey('id', $note);
        $deleted = $this->notesService->delete($id);
        $newNote = $this->notesService->getOne($id);
        $this->assertFalse($newNote);
        $this->assertTrue($deleted);

        //not for owner
        $id = 2;
        $userid = 1;

        $note = $this->notesService->getOne($id);
        $this->assertArrayHasKey('id', $note);
        $deleted = $this->notesService->delete($id, $userid);
        $newNote = $this->notesService->getOne($id);
        $this->assertArrayHasKey('id', $newNote);
        $this->assertFalse($deleted);
    }

    public function testDeleteNonexistent()
    {
        $id = 999;

        $deleted = $this->notesService->delete($id);
        $this->assertFalse($deleted);
    }
}
