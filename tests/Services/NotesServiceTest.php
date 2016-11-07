<?php

namespace Solohin\ToptalExam\Tests\Services;

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Solohin\ToptalExam\Database\SchemaManager;
use Solohin\ToptalExam\Services\NotesService;
use Solohin\ToptalExam\Services\UsersService;


class NotesServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var $notesService UsersService */
    private $notesService;

    public function setUp()
    {
        $app = new Application();
        $app->register(new DoctrineServiceProvider(), array(
            "db.options" => array(
                "driver" => "pdo_sqlite",
                "memory" => true
            ),
        ));

        $schemaManager = new SchemaManager($app['db']);
        $schemaManager->flushDatabase();

        $this->notesService = new NotesService($app['db']);

        $this->notesService->insert([
            'text' => 'First note',
            'calories' => 100,
            'user_id' => 2,
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
    }

    public function testGetOne()
    {
        //TODO implement
    }
    public function testGetOneWithUserID()
    {
        //TODO implement
    }
    public function testGetOneNonexistent()
    {
        //TODO implement
    }
    public function testGetAll()
    {
        //TODO implement
    }
    public function testGetAllWithUserID()
    {
        //TODO implement
    }
    public function testGetAllWithDates()
    {
        //TODO implement
    }
    public function testGetAllWithTime()
    {
        //TODO implement
    }
    public function testInsert()
    {
        //TODO implement
    }
    public function testInsertRequiredParams()
    {
        //TODO implement
    }
    public function testUpdate()
    {
        //TODO implement
    }
    public function testUpdateWithUserID()
    {
        //TODO implement
    }
    public function testUpdateNonexistent()
    {
        //TODO implement
    }
    public function testDelete()
    {
        //TODO implement
    }
    public function testDeleteNonexistent()
    {
        //TODO implement
    }
}
