<?php

namespace Solohin\ToptalExam;

use Silex\Application;

class ServicesLoader
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function bindServicesIntoContainer()
    {
        $this->app['users.service'] = function () {
            return new Services\UsersService($this->app["db"]);
        };
        $this->app['notes.service'] = function () {
            return new Services\NotesService($this->app["db"]);
        };
    }
}

