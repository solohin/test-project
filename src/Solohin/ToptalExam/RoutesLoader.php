<?php

namespace Solohin\ToptalExam;

use Silex\Application;

class RoutesLoader
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->instantiateControllers();
    }

    private function instantiateControllers()
    {
        $this->app['notes.controller'] = function () {
            return new Controllers\NotesController($this->app['notes.service']);
        };
        $this->app['login.controller'] = function () {
            return new Controllers\LoginController($this->app['users.service'], $this->app);
        };
    }

    public function bindRoutesToControllers()
    {
        $api = $this->app["controllers_factory"];

        $this->bindNotes($api);
        $this->bindLogin($api);

        $this->app->mount('/' . $this->app["api.version"], $api);
    }

    private function bindLogin(\Silex\ControllerCollection &$api)
    {
        $api->post('/login', "login.controller:login");
    }

    private function bindNotes(\Silex\ControllerCollection &$api)
    {
        $api->get('/notes', "notes.controller:getAll");
        $api->get('/notes/{id}', "notes.controller:getOne");
        $api->post('/notes', "notes.controller:save");
        $api->put('/notes/{id}', "notes.controller:update");
        $api->delete('/notes/{id}', "notes.controller:delete");
    }
}

