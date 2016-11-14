<?php

namespace Solohin\ToptalExam;

use Silex\Application;
use Silex\ControllerCollection;

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
        $this->app['login.controller'] = function () {
            return new Controllers\LoginController($this->app['users.service']);
        };
        $this->app['registration.controller'] = function () {
            return new Controllers\RegistrationController($this->app['users.service']);
        };
        $this->app['notes.controller'] = function () {
            return new Controllers\NotesController($this->app['notes.service'], $this->app);
        };
        $this->app['users.controller'] = function () {
            return new Controllers\UsersController($this->app['users.service'], $this->app);
        };
    }

    public function bindRoutesToControllers()
    {
        $api = $this->app["controllers_factory"];

        $this->bindLogin($api);
        $this->bindRegister($api);
        $this->bindNotes($api);
        $this->bindUsers($api);

        $this->app->mount('/' . $this->app["api.version"], $api);
    }

    private function bindLogin(ControllerCollection &$api)
    {
        $api->post('/login', "login.controller:login");
    }

    private function bindRegister(ControllerCollection &$api)
    {
        $api->post('/register', "registration.controller:register");
    }

    private function bindNotes(ControllerCollection &$api)
    {
        $api->post('/notes/', "notes.controller:add");
        $api->put('/notes/{id}', "notes.controller:update")->assert('id', '\d+');
        $api->get('/notes/{id}', "notes.controller:getOne")->assert('id', '\d+');
        $api->get('/notes/', "notes.controller:getList");
        $api->delete('/notes/{id}', "notes.controller:remove")->assert('id', '\d+');

        //no-slash aliases
        $api->get('/notes', "notes.controller:getList");
        $api->post('/notes', "notes.controller:add");
    }

    private function bindUsers(ControllerCollection &$api)
    {
        //me routes
        $api->delete('/users/me', "users.controller:removeMe");
        $api->get('/users/me', "users.controller:getMe");
        $api->put('/users/me', "users.controller:updateMe");

        //basic routes
        $api->delete('/users/{id}', "users.controller:remove")->assert('id', '\d+');
        $api->get('/users/{id}', "users.controller:getOne")->assert('id', '\d+');
        $api->get('/users/', "users.controller:getAll");
        $api->put('/users/{id}', "users.controller:update")->assert('id', '\d+');

        //no-slash aliases
        $api->get('/users', "users.controller:getAll");
    }
}

