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
        $this->app['login.controller'] = function () {
            return new Controllers\LoginController($this->app['users.service']);
        };
        $this->app['registration.controller'] = function () {
            return new Controllers\RegistrationController($this->app['users.service']);
        };
    }

    public function bindRoutesToControllers()
    {
        $api = $this->app["controllers_factory"];

        $this->bindLogin($api);

        $this->app->mount('/' . $this->app["api.version"], $api);
    }

    private function bindLogin(\Silex\ControllerCollection &$api)
    {
        $api->post('/login', "login.controller:login");
        $api->post('/register', "registration.controller:register");
    }
}

