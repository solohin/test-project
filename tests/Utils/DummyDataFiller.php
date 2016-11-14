<?php
namespace Solohin\ToptalExam\Tests\Utils;

require_once __DIR__ . '/../../vendor/autoload.php';


use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Solohin\ToptalExam\Database\SchemaManager;
use Solohin\ToptalExam\Services\UsersService;
use Solohin\ToptalExam\Services\NotesService;

class DummyDataFiller
{
    /** @var  UsersService */
    private $usersService;
    /** @var  NotesService */
    private $notesService;

    public function start()
    {
        $app = new Application();
        $app->register(new DoctrineServiceProvider(),
            ["db.options" => \Solohin\ToptalExam\Tests\Utils\DataBase::getTestDBParams()]
        );
        $schemaManager = new SchemaManager($app['db']);
        $schemaManager->flushDatabase();

        $this->usersService = new UsersService($app['db']);
        $this->notesService = new NotesService($app['db']);

        $this->fillNotes();
        $this->fillUsers();
    }

    public function fillNotes()
    {
        $hour = 60 * 60;
        $day = $hour * 24;

        $userIds = [1, 4];

        $ts = time() - 10 * $day;
        $tillTs = time() + 10 * $day;
        $counter = 1;
        while ($ts < $tillTs) {
            $ts += rand(2 * $hour, 8 * $hour);

            $note = [
                'user_id' => $userIds[array_rand($userIds)],
                'text' => 'Dummy meal #' . $counter++,
                'calories' => rand(400, 800),
                'date' => $this->timestampToDate($ts),
                'time' => $this->secondsToTimeString($ts),
            ];

            $this->notesService->insert($note);

            printf("Note %s created\n", $note['text']);
        }
    }

    private function timestampToDate($timestamp)
    {
        return date('d.m.Y', $timestamp);
    }

    private function secondsToTimeString($seconds)
    {
        $hours = floor($seconds / (60 * 60));
        if ($hours >= 24) {
            $hours = $hours % 24;
        }
        $minutes = round(($seconds % (60 * 60)) / 60);
        if ($minutes >= 60) {
            $minutes = $minutes % 60;
        }

        return sprintf("%02d", $hours) . ':' . sprintf("%02d", $minutes);
    }

    public function fillUsers()
    {
        $users = [
            'user' => [
                'username' => 'dummyUser',
                'password' => 'Dummy Password 12345',
                'roles' => ['ROLE_USER'],
                'daily_normal' => 1500
            ],
            'admin' => [
                'username' => 'dummyAdmin',
                'password' => 'Dummy Password 12345',
                'roles' => ['ROLE_ADMIN'],
                'daily_normal' => 2000
            ],
            'manager' => [
                'username' => 'dummyManager',
                'password' => 'Dummy Password 12345',
                'roles' => ['ROLE_MANAGER'],
                'daily_normal' => 2000
            ],
            'user2' => [
                'username' => 'dummyUser2',
                'password' => 'Dummy Password 12345',
                'roles' => ['ROLE_USER'],
                'daily_normal' => 3500
            ]
        ];
        foreach ($users as $user) {
            $this->usersService->insert($user);
            printf("User %s created\n", $user['username']);
        }
    }
}

(new DummyDataFiller())->start();