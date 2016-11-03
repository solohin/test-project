<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 02/11/16
 * Time: 02:43
 */
namespace Solohin\ToptalExam\Security;

use Solohin\ToptalExam\Database\UserProvider;

class SecurityLoader
{
    private $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    public function initTokenSecurity()
    {
//        $this->app['app.login_password_authenticator'] = function ($app) {
//            return new LoginPasswordAuthenticator($app['security.encoder_factory']);
//        };
//        $this->app->register(
//            new \Silex\Provider\SecurityServiceProvider(), [
//                'security.firewalls' => [
//                    'register' => [ //only /v1/register
//                        'pattern' => '^' . preg_quote('/' . $this->app["api.version"] . '/') . 'login'
//                    ],
//                ]
//            ]
//        );
    }
}