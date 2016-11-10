<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 10/11/16
 * Time: 15:05
 */

namespace Solohin\ToptalExam\Tests\API\Users;

use Silex\WebTestCase;
use Solohin\ToptalExam\Services\UsersService;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class UsersTestTemplate extends WebTestCase
{
    /** @var UsersService */
    protected $usersService;

    protected $dummyUsers = [
        'user' => [
            'username' => 'dummyUser',
            'password' => 'Dummy Password 12345',
            'roles' => ['ROLE_USER'],
            'daily_normal' => 400
        ],
        'admin' => [
            'username' => 'dummyAdmin',
            'password' => 'Dummy Password 12345',
            'roles' => ['ROLE_ADMIN'],
            'daily_normal' => 400
        ],
        'manager' => [
            'username' => 'dummyManager',
            'password' => 'Dummy Password 12345',
            'roles' => ['ROLE_MANAGER'],
            'daily_normal' => 400
        ],
        'user2' => [
            'username' => 'dummyUser2',
            'password' => 'Dummy Password 12345',
            'roles' => ['ROLE_USER'],
            'daily_normal' => 400
        ]
    ];

    private function insertDummyUsers()
    {
        $this->usersService = new UsersService($this->app['db']);

        //Create users
        $id = $this->usersService->insert($this->dummyUsers['user']);
        $this->dummyUsers['user'] = $this->usersService->getOne($id);

        $id = $this->usersService->insert($this->dummyUsers['admin']);
        $this->dummyUsers['admin'] = $this->usersService->getOne($id);

        $id = $this->usersService->insert($this->dummyUsers['manager']);
        $this->dummyUsers['manager'] = $this->usersService->getOne($id);

        $this->usersService->insert($this->dummyUsers['user2']);
    }

    public function setUp()
    {
        parent::setUp();

        $this->app['schema_manager']->flushDatabase();

        $this->insertDummyUsers();
    }

    public function tearDown()
    {
        //TODO FLUSH database
        parent::tearDown();
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