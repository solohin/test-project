<?php

namespace Solohin\ToptalExam\Tests\Services;

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Solohin\ToptalExam\Database\SchemaManager;
use Solohin\ToptalExam\Services\UsersService;


class UsersServiceTest extends \PHPUnit_Framework_TestCase
{

    /** @var $usersService UsersService */
    private $usersService;

    public function setUp()
    {
        $app = new Application();
        $app->register(new DoctrineServiceProvider(),
            ["db.options" => \Solohin\ToptalExam\Tests\Utils\DataBase::getTestDBParams()]
        );
        $schemaManager = new SchemaManager($app['db']);
        $schemaManager->flushDatabase();

        $this->usersService = new UsersService($app['db']);

        $this->usersService->insert([
            'id' => 1,
            'username' => 'test 1',
            'password' => 'testpass',
            'token' => 'some token1'
        ]);

        $this->usersService->insert([
            'id' => 2,
            'username' => 'test 2',
            'password' => 'testpass2',
            'token' => 'some token2'
        ]);
    }

    public function testGetOne()
    {
        $firstItem = $this->usersService->getOne(1);
        $secondItem = $this->usersService->getOne(2);

        $this->assertEquals('test 1', $firstItem['username']);
        $this->assertNotEquals('testpass', $firstItem['password']);
        $this->assertNotEquals('some token1', $firstItem['token']);
        $this->assertEquals('1', $firstItem['id']);


        $this->assertEquals('test 2', $secondItem['username']);
        $this->assertNotEquals('testpass', $secondItem['password']);
        $this->assertNotEquals('some token2', $secondItem['token']);
        $this->assertEquals('2', $secondItem['id']);

        $thirdItem = $this->usersService->getOne(99);
        $this->assertFalse($thirdItem);
    }

    public function testIsTokenExists()
    {
        $firstItem = $this->usersService->getOne(1);

        $this->assertFalse(
            $this->usersService->isTokenExists('this token not exists')
        );
        $this->assertTrue(
            $this->usersService->isTokenExists($firstItem['token'])
        );
    }

    public function testGetByUsername()
    {
        $firstItem = $this->usersService->getOne(2);

        $foundByRightName = $this->usersService->getByUsername($firstItem['username']);
        $foundByWrongName = $this->usersService->getByUsername('wrong name');
        $this->assertEquals('2', $foundByRightName['id']);
        $this->assertEquals(false, $foundByWrongName);
    }

    public function testInsert()
    {
        $insertId = $this->usersService->insert([
            'username' => 'test 3',
            'password' => 'testpass2',
            'token' => 'some token2'
        ]);

        $testUser = $this->usersService->getOne($insertId);

        $this->assertEquals((string)$insertId, $testUser['id'], 'Test id = ' . $insertId . ', test user = ' . print_r($testUser, 1));
        $this->assertEquals(['ROLE_USER'], $testUser['roles']);
    }

    public function testDailyNormal()
    {
        //default
        $insertId = $this->usersService->insert([
            'username' => 'test 33',
            'password' => 'testpass2',
            'token' => 'some token2',
            'roles' => ['ROLE_ADMIN', 'ROLE_MANAGER']
        ]);
        $testUser = $this->usersService->getOne($insertId);

        $this->assertEquals((string)$insertId, $testUser['id'], 'Test id = ' . $insertId . ', test user = ' . print_r($testUser, 1));
        $this->assertEquals(2000, $testUser['daily_normal']);

        //custom
        $insertId = $this->usersService->insert([
            'username' => 'test 34',
            'password' => 'testpass2',
            'token' => 'some token2',
            'daily_normal' => 5000,
            'roles' => ['ROLE_ADMIN', 'ROLE_MANAGER']
        ]);
        $testUser = $this->usersService->getOne($insertId);

        $this->assertEquals((string)$insertId, $testUser['id'], 'Test id = ' . $insertId . ', test user = ' . print_r($testUser, 1));
        $this->assertEquals(5000, $testUser['daily_normal']);

        //zero as is
        $insertId = $this->usersService->insert([
            'username' => 'test 35',
            'password' => 'testpass2',
            'token' => 'some token2',
            'daily_normal' => 0,
            'roles' => ['ROLE_ADMIN', 'ROLE_MANAGER']
        ]);
        $testUser = $this->usersService->getOne($insertId);

        $this->assertEquals((string)$insertId, $testUser['id'], 'Test id = ' . $insertId . ', test user = ' . print_r($testUser, 1));
        $this->assertEquals(0, $testUser['daily_normal']);

        //-1 is zero
        $insertId = $this->usersService->insert([
            'username' => 'test 353',
            'password' => 'testpass2',
            'token' => 'some token2',
            'daily_normal' => -1,
            'roles' => ['ROLE_ADMIN', 'ROLE_MANAGER']
        ]);
        $testUser = $this->usersService->getOne($insertId);

        $this->assertEquals((string)$insertId, $testUser['id'], 'Test id = ' . $insertId . ', test user = ' . print_r($testUser, 1));
        $this->assertEquals(0, $testUser['daily_normal']);

        //wrong values to 0
        $insertId = $this->usersService->insert([
            'username' => 'test 888',
            'password' => 'testpass2',
            'token' => 'some token2',
            'daily_normal' => 'wrong value',
            'roles' => ['ROLE_ADMIN', 'ROLE_MANAGER']
        ]);
        $testUser = $this->usersService->getOne($insertId);

        $this->assertEquals((string)$insertId, $testUser['id'], 'Test id = ' . $insertId . ', test user = ' . print_r($testUser, 1));
        $this->assertEquals(0, $testUser['daily_normal']);
    }

    public function testInsertMultipleRoles()
    {
        $insertId = $this->usersService->insert([
            'username' => 'test 3',
            'password' => 'testpass2',
            'token' => 'some token2',
            'roles' => ['ROLE_ADMIN', 'ROLE_MANAGER']
        ]);
        $testUser = $this->usersService->getOne($insertId);

        $this->assertEquals((string)$insertId, $testUser['id'], 'Test id = ' . $insertId . ', test user = ' . print_r($testUser, 1));
        $this->assertEquals(['ROLE_ADMIN'], $testUser['roles']);
    }

    public function testUpdate()
    {
        $userName = 'new username';
        $token = 'new token';
        $password = 'new password';

        $this->usersService->update(2, [
            'username' => $userName,
            'password' => $password,
            'token' => $token
        ]);

        $newUser = $this->usersService->getOne(2);

        $this->assertEquals($userName, $newUser['username']);
        $this->assertNotEquals($password, $newUser['password']);
        $this->assertEquals($token, $newUser['token']);
    }

    public function testDelete()
    {
        $this->usersService->delete('2');

        $deletedUser = $this->usersService->getOne('2');
        $this->assertEquals(false, $deletedUser);
    }

}
