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

class TokenTest extends WebTestCase
{
    private $testToken;

    public function testCorrectToken()
    {
        $client = $this->createClient();
        $client->request('POST', '/v1/dummy_method', [], [], ['HTTP_X-AUTH-TOKEN' => $this->testToken]);

        $rawResponse = $client->getResponse()->getContent();
        $responseData = json_decode($rawResponse, 1);

        $this->assertArrayHasKey('error_type', $responseData, 'Raw response is ' . $rawResponse);
        $this->assertEquals('method_not_found', $responseData['error_type'], 'error_message is ' . $responseData['error_message']);
        $this->assertEquals(false, $responseData['success']);
    }

    public function testWrongToken()
    {
        $client = $this->createClient();
        $client->request('POST', '/v1/dummy_method', [], [], ['HTTP_X-AUTH-TOKEN' => 'wrong token']);

        $rawResponse = $client->getResponse()->getContent();
        $responseData = json_decode($rawResponse, 1);

        $this->assertArrayHasKey('error_type', $responseData, 'Raw response is ' . $rawResponse);
        $this->assertEquals('wrong_token', $responseData['error_type'], 'error_message is ' . $responseData['error_message']);
        $this->assertEquals(false, $responseData['success']);
    }

    public function testEmptyToken()
    {
        $client = $this->createClient();
        $client->request('POST', '/v1/dummy_method');

        $rawResponse = $client->getResponse()->getContent();
        $responseData = json_decode($rawResponse, 1);

        $this->assertArrayHasKey('error_type', $responseData, 'Raw response is ' . $rawResponse);
        $this->assertEquals('no_token', $responseData['error_type'], 'Raw response is ' . $rawResponse);
        $this->assertEquals(false, $responseData['success']);
    }

    public function testQueryParamToken()
    {
        $client = $this->createClient();
        $client->request('POST', '/v1/dummy_method?token=' . $this->testToken);

        $rawResponse = $client->getResponse()->getContent();
        $responseData = json_decode($rawResponse, 1);

        $this->assertArrayHasKey('error_type', $responseData, 'Raw response is ' . $rawResponse);
        $this->assertEquals('method_not_found', $responseData['error_type'], 'error_message is ' . $responseData['error_message']);
        $this->assertEquals(false, $responseData['success']);
    }

    public function setUp()
    {
        parent::setUp();
        $this->app['schema_manager']->flushDatabase();

        $client = $this->createClient();

        //Generate test token
        $client->request('POST', '/v1/register', [
            'username' => 'vasya',
            'password' => 'pupkin',
        ]);
        $response = $client->getResponse();
        $rawResponse = $response->getContent();
        $responseData = json_decode($rawResponse, 1);

        $this->testToken = $responseData['token'];
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