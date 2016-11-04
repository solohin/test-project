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

class RegistrationTest extends WebTestCase
{
    const MIN_USERNAME = 4;
    const MAX_USERNAME = 12;
    const MIN_PASSWORD = 6;
    const MAX_PASSWORD = 64;

    const DUMMY_USER = [
        'username' => 'DummyUser',
        'password' => 'Dummy Password 12345',
        'roles' => 'NO_ROLES',
        'token' => 'old token',
    ];

    public function testCorrectCredentials()
    {
        $this->registerHaveToBeSuccesful(str_repeat('_', self::MIN_USERNAME), str_repeat('_', self::MIN_PASSWORD));
        $this->registerHaveToBeSuccesful(str_repeat('1', self::MAX_USERNAME), str_repeat('_', self::MAX_PASSWORD));
        $this->registerHaveToBeSuccesful(str_repeat('2', self::MIN_USERNAME), str_repeat('_', self::MAX_PASSWORD));
        $this->registerHaveToBeSuccesful(str_repeat('3', self::MAX_USERNAME), str_repeat('_', self::MIN_PASSWORD));
    }

    public function testExisting()
    {
        $userName = 'test123';
        $password = str_repeat('_', self::MIN_PASSWORD);
        $otherPassword = str_repeat('1', self::MIN_PASSWORD);

        $this->registerHaveToBeSuccesful($userName, $password);
        $this->registerHaveToThrowError($userName, $otherPassword, ['username', 'exists']);
    }

    public function testWhitespaces()
    {
        $userName = '     ';
        $password = str_repeat('_', self::MIN_PASSWORD);

        $this->registerHaveToThrowError($userName, $password, ['username', self::MIN_USERNAME]);
    }

    public function testShortCredentials()
    {
        $this->registerHaveToThrowError(
            str_repeat('_', self::MIN_USERNAME - 1),
            str_repeat('_', self::MIN_PASSWORD),
            ['username', self::MIN_USERNAME]
        );
        $this->registerHaveToThrowError(
            str_repeat('_', self::MIN_USERNAME),
            str_repeat('_', self::MIN_PASSWORD - 1),
            ['password', self::MIN_PASSWORD]
        );
        $this->registerHaveToThrowError(
            str_repeat('_', self::MIN_USERNAME - 1),
            str_repeat('_', self::MIN_PASSWORD - 1),
            ['username', self::MIN_USERNAME]
        );
    }

    public function testEmptyCredentials()
    {
        $this->registerHaveToThrowError(
            '',
            '',
            ['username', self::MIN_USERNAME]
        );
    }

    public function testLongCredentials()
    {
        $this->registerHaveToThrowError(
            str_repeat('_', self::MAX_USERNAME + 1),
            str_repeat('_', self::MAX_PASSWORD),
            ['username', self::MAX_USERNAME]
        );
        $this->registerHaveToThrowError(
            str_repeat('_', self::MAX_USERNAME),
            str_repeat('_', self::MAX_PASSWORD + 1),
            ['password', self::MIN_PASSWORD]
        );
        $this->registerHaveToThrowError(
            str_repeat('_', self::MAX_USERNAME + 1),
            str_repeat('_', self::MAX_PASSWORD + 1),
            ['username', self::MAX_USERNAME]
        );
    }

    private function registerHaveToThrowError($username, $password, array $errorWords = [])
    {
        $client = $this->createClient();

        $client->request('POST', '/v1/register', [
            'username' => $username,
            'password' => $password,
        ]);

        $response = $client->getResponse();
        $rawResponse = $response->getContent();
        $responseData = json_decode($rawResponse, 1);

        $this->assertTrue($client->getResponse()->isOk(), 'Response code is ' . $client->getResponse()->getStatusCode() . "\n" . 'Raw response is ' . $rawResponse);
        $this->assertArrayNotHasKey('token', $responseData, 'Raw response is ' . $rawResponse);
        $this->assertFalse($responseData['success'], 'Raw response is ' . $rawResponse);

        foreach ($errorWords as $word) {
            $this->assertContains(mb_strtolower($word), mb_strtolower($responseData['error_message']));
        }
    }

    private function registerHaveToBeSuccesful($username, $password)
    {
        $client = $this->createClient();

        $client->request('POST', '/v1/register', [
            'username' => $username,
            'password' => $password,
        ]);

        $response = $client->getResponse();
        $rawResponse = $response->getContent();
        $responseData = json_decode($rawResponse, 1);

        $this->assertTrue($client->getResponse()->isOk(), 'Response code is ' . $client->getResponse()->getStatusCode() . "\n" . 'Raw response is ' . $rawResponse);
        $this->assertArrayHasKey('token', $responseData, 'Raw response is ' . $rawResponse);
        $this->assertTrue($responseData['success'], 'Raw response is ' . $rawResponse);
    }

    public function setUp()
    {
        parent::setUp();

        $this->app['schema_manager']->flushDatabase();
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