<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 07/11/16
 * Time: 18:33
 */

namespace Solohin\ToptalExam\Tests\API\Notes;

use Silex\WebTestCase;
use Solohin\ToptalExam\Services\NotesService;
use Solohin\ToptalExam\Services\UsersService;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class NotesTestTemplate extends WebTestCase
{
    protected $dummyUsers = [
        'user' => [
            'username' => 'dummyUser',
            'password' => 'Dummy Password 12345',
            'roles' => ['ROLE_USER'],
        ],
        'admin' => [
            'username' => 'dummyAdmin',
            'password' => 'Dummy Password 12345',
            'roles' => ['ROLE_ADMIN'],
        ],
        'manager' => [
            'username' => 'dummyManager',
            'password' => 'Dummy Password 12345',
            'roles' => ['ROLE_MANAGER'],
        ],
        'user2' => [
            'username' => 'dummyUser2',
            'password' => 'Dummy Password 12345',
            'roles' => ['ROLE_USER'],
        ]
    ];

    /*
     * 100 - 1 - 12:00 - 01.01.2017
     * 200 - 2 - 14:00 - 02.01.2017
     * 300 - 1 - 17:40 - 01.01.2017
     * 400 - 1 - 16:05 - 02.01.2017
     * 900 - 1 - 23:59 - 01.01.2017
     * 1200 - 1 - 00:00 - 03.01.2017
     */

    private function insertDummyNotes()
    {
        $notesService = new NotesService($this->app['db']);

        $notesService->insert([
            'text' => 'First note',
            'calories' => 100,
            'user_id' => 1,
            'time' => '12:00',
            'date' => '01.01.2017',
        ]);

        $notesService->insert([
            'text' => 'Second note',
            'calories' => 200,
            'user_id' => 4,
            'time' => '14:00',
            'date' => '02.01.2017',
        ]);

        $notesService->insert([
            'text' => 'Third note',
            'calories' => 300,
            'user_id' => 1,
            'time' => '17:40',
            'date' => '01.01.2017',
        ]);

        $notesService->insert([
            'text' => 'Fourth note',
            'calories' => 400,
            'user_id' => 1,
            'time' => '16:05',
            'date' => '02.01.2017',
        ]);
        $notesService->insert([
            'text' => 'Fifth note',
            'calories' => 900,
            'user_id' => 1,
            'time' => '23:59',
            'date' => '01.01.2017',
        ]);
        $notesService->insert([
            'text' => 'Sixth note',
            'calories' => 1200,
            'user_id' => 1,
            'time' => '00:00',
            'date' => '03.01.2017',
        ]);
    }

    private function insertDummyUsers()
    {
        $usersService = new UsersService($this->app['db']);

        //Create users
        $id = $usersService->insert($this->dummyUsers['user']);
        $this->dummyUsers['user'] = $usersService->getOne($id);

        $id = $usersService->insert($this->dummyUsers['admin']);
        $this->dummyUsers['admin'] = $usersService->getOne($id);

        $id = $usersService->insert($this->dummyUsers['manager']);
        $this->dummyUsers['manager'] = $usersService->getOne($id);
    }

    public function setUp()
    {
        parent::setUp();

        $this->app['schema_manager']->flushDatabase();


        $this->insertDummyNotes();
        $this->insertDummyUsers();
    }

    public function tearDown()
    {
        //TODO FLUSH database
        parent::tearDown();
    }

    protected function caloriesSumm500Notes($role = 'admin')
    {
        $notes = $this->get500Notes($role);
        return array_sum(array_column($notes, 'calories'));
    }

    protected function get500Notes($role = 'admin')
    {
        return $this->makeJsonRequest('/v1/notes', 'GET', $role, [], true)['notes'];
    }

    protected function makeJsonRequest($path, $type, $role, $data, $waitForOk)
    {
        $client = $this->createClient();

        $client->request(strtoupper($type), $path, $data, [], [
            'HTTP_X-AUTH-TOKEN' => $this->dummyUsers[$role]['token']
        ]);

        $response = $client->getResponse();
        $rawResponse = $response->getContent();

        $this->assertEquals($waitForOk, $client->getResponse()->isOk(), 'Response code is ' . $client->getResponse()->getStatusCode() . "\n" . 'Raw response is ' . $rawResponse);

        $responseData = json_decode($rawResponse, 1);
        $this->assertArrayHasKey('success', $responseData, $rawResponse);
        $this->assertEquals($waitForOk, $responseData['success'], $rawResponse);

        if (!$waitForOk) {
            $this->assertArrayHasKey('error_type', $responseData);
            $this->assertArrayHasKey('error_message', $responseData);
        }

        return $responseData;
    }

    /**
     * Creates the application.
     *
     * @return HttpKernelInterface
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../../../web/index.php';
        unset($app['exception_handler']);
        return $app;
    }
}