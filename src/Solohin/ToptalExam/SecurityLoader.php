<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 02/11/16
 * Time: 02:43
 */
namespace Solohin\ToptalExam;

use Solohin\ToptalExam\Security\TokenAuthenticator;
use Solohin\ToptalExam\Security\UserProvider;
use Solohin\ToptalExam\Services\UsersService;

class SecurityLoader
{
    private $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    public function initTokenSecurity()
    {
        $this->app['app.apikey_authenticator'] = function ($app) {
            return new TokenAuthenticator($app['users.service']);
        };
        $this->app->register(
            new \Silex\Provider\SecurityServiceProvider(), [
                'security.firewalls' => [
                    'login' => [
                        'pattern' => '^' . preg_quote('/' . $this->app["api.version"] . '/') . 'login'
                    ],
                    'register' => [
                        'pattern' => '^' . preg_quote('/' . $this->app["api.version"] . '/') . 'register'
                    ],
                    'main' => array(
                        'pattern' => '^' . preg_quote('/' . $this->app["api.version"] . '/') . '',
                        'guard' => array(
                            'authenticators' => array(
                                'app.apikey_authenticator'
                            ),
                        ),
                        'users' => function (\Silex\Application $app) {
                            return new UserProvider($app['users.service']);
                        },
                    ),
                ]
            ]
        );
    }
}