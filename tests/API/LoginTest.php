<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 03/11/16
 * Time: 14:41
 */
namespace Solohin\ToptalExam\Tests\API;

use Silex\WebTestCase;
use Solohin\ToptalExam\Services\UsersService;

class LoginTest extends WebTestCase
{
    const DUMMY_USER = [
        'username' => 'DummyUser',
        'password' => 'Dummy Password 12345',
        'roles' => 'ROLE_USER',
        'token' => 'old token',
    ];

    public function testCorrectCredentials()
    {
        $client = $this->createClient();

        $client->request('POST', '/v1/login', [
            'username' => self::DUMMY_USER['username'],
            'password' => self::DUMMY_USER['password'],
        ]);

        $response = $client->getResponse();
        $rawResponse = $response->getContent();
        $responseData = json_decode($rawResponse, 1);

        $this->assertTrue($client->getResponse()->isOk(), 'Response code is ' . $client->getResponse()->getStatusCode() . "\n" . 'Raw response is ' . $rawResponse);
        $this->assertArrayHasKey('success', $responseData, 'Raw response is ' . $rawResponse);
        $this->assertArrayHasKey('token', $responseData, 'Raw response is ' . $rawResponse);
        $this->assertArrayHasKey('roles', $responseData, 'Raw response is ' . $rawResponse);
        $this->assertEquals(['ROLE_USER'], $responseData['roles'], 'Raw response is ' . $rawResponse);
        $this->assertTrue($responseData['success'], 'Raw response is ' . $rawResponse);
    }

    public function testTokenRefreshed()
    {
        $client = $this->createClient();

        $client->request('POST', '/v1/login', [
            'username' => self::DUMMY_USER['username'],
            'password' => self::DUMMY_USER['password'],
        ]);

        $response = $client->getResponse();
        $rawResponse = $response->getContent();
        $responseData = json_decode($rawResponse, 1);


        $this->assertArrayHasKey('token', $responseData, 'Raw response is ' . $rawResponse);
        $this->assertNotEmpty($responseData['token']);
        $this->assertNotEquals(self::DUMMY_USER['token'], $responseData['token']);

    }

    public function testIncorrectPassword()
    {
        $client = $this->createClient();

        $client->request('POST', '/v1/login', [
            'username' => self::DUMMY_USER['username'],
            'password' => self::DUMMY_USER['password'] . ' and other symbols',
        ]);

        $this->assertTrue($client->getResponse()->isOk(), 'Response code is ' . $client->getResponse()->getStatusCode());

        $response = $client->getResponse();
        $rawResponse = $response->getContent();
        $responseData = json_decode($rawResponse, 1);

        $this->assertArrayHasKey('success', $responseData, 'Raw response is ' . $rawResponse);
        $this->assertFalse($responseData['success'], 'Raw response is ' . $rawResponse);
        $this->assertArrayNotHasKey('token', $responseData, 'Raw response is ' . $rawResponse);
        $this->assertArrayNotHasKey('token', $responseData, 'Raw response is ' . $rawResponse);

        $this->assertArrayHasKey('error_type', $responseData, 'Raw response is ' . $rawResponse);
        $this->assertEquals('wrong_password', $responseData['error_type'], 'error_type is ' . $responseData['error_message']);
    }

    public function testIncorrectLogin()
    {
        $client = $this->createClient();

        $client->request('POST', '/v1/login', [
            'username' => self::DUMMY_USER['username'] . ' and other symbols',
            'password' => self::DUMMY_USER['password'],
        ]);

        $this->assertTrue($client->getResponse()->isOk(), 'Response code is ' . $client->getResponse()->getStatusCode());

        $response = $client->getResponse();
        $rawResponse = $response->getContent();
        $responseData = json_decode($rawResponse, 1);

        $this->assertArrayHasKey('success', $responseData, 'Raw response is ' . $rawResponse);
        $this->assertFalse($responseData['success'], 'Raw response is ' . $rawResponse);
        $this->assertArrayNotHasKey('token', $responseData, 'Raw response is ' . $rawResponse);

        $this->assertArrayHasKey('error_type', $responseData, 'Raw response is ' . $rawResponse);
        $this->assertEquals('wrong_username', $responseData['error_type'], 'error_type is ' . $responseData['error_message']);
    }

    public function setUp()
    {
        parent::setUp();

        $this->app['schema_manager']->flushDatabase();
        $usersService = new UsersService($this->app['db']);
        $usersService->insert(self::DUMMY_USER);
    }

    public function tearDown()
    {
        //TODO FLUSH database
        parent::tearDown();
    }

    public function createApplication()
    {
        $app = require __DIR__ . '/../../web/index.php';
        unset($app['exception_handler']);
        return $app;
    }
}