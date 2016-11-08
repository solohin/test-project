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
use Solohin\ToptalExam\Services\UsersService;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class NotesTestTemplate extends WebTestCase
{
    protected $dummyUsers = [
        'user' => [
            'username' => 'dummyUser',
            'password' => 'Dummy Password 12345',
            'roles' => ['ROLE_USER'],
            'token' => 'old token',
        ],
        'admin' => [
            'username' => 'dummyAdmin',
            'password' => 'Dummy Password 12345',
            'roles' => ['ROLE_ADMIN'],
            'token' => 'old token',
        ],
        'manager' => [
            'username' => 'dummyManager',
            'password' => 'Dummy Password 12345',
            'roles' => ['ROLE_MANAGER'],
            'token' => 'old token',
        ]
    ];


    public function setUp()
    {
        parent::setUp();

        $this->app['schema_manager']->flushDatabase();
        $usersService = new UsersService($this->app['db']);

        //Create users
        $id = $usersService->insert($this->dummyUsers['user']);
        $this->dummyUsers['user'] = $usersService->getOne($id);

        $id = $usersService->insert($this->dummyUsers['admin']);
        $this->dummyUsers['admin'] = $usersService->getOne($id);

        $id = $usersService->insert($this->dummyUsers['manager']);
        $this->dummyUsers['manager'] = $usersService->getOne($id);
    }

    public function tearDown()
    {
        //TODO FLUSH database
        parent::tearDown();
    }

    protected function caloriesSumm500Notes($role = 'admin'){
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