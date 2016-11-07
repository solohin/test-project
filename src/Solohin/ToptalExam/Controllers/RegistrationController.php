<?php

namespace Solohin\ToptalExam\Controllers;

use Solohin\ToptalExam\ErrorTypes;
use Solohin\ToptalExam\Security\UserRoles;
use Symfony\Component\HttpFoundation\JsonResponse;
use Solohin\ToptalExam\Services\UsersService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;


class RegistrationController
{
    const MIN_USERNAME = 4;
    const MAX_USERNAME = 32;
    const MIN_PASSWORD = 6;
    const MAX_PASSWORD = 64;

    private $usersService;

    public function __construct(UsersService $service)
    {
        $this->usersService = $service;
    }

    public function register(Request $request, \Silex\Application $app)
    {
        $response = ['success' => true];

        $username = trim($request->request->get('username'));
        $password = trim($request->request->get('password'));

        if (strlen($username) < self::MIN_USERNAME) {
            $response['error_message'] = 'Username too short. It have to be at least ' . self::MIN_USERNAME . ' symbols';
            $response['error_type'] = ErrorTypes::SHORT_USERNAME;
        } elseif (strlen($username) > self::MAX_USERNAME) {
            $response['error_message'] = 'Username too long. It have to be ' . self::MAX_USERNAME . ' symbols max';
            $response['error_type'] = ErrorTypes::LONG_USERNAME;
        } elseif (strlen($password) < self::MIN_PASSWORD) {
            $response['error_message'] = 'Password too short. It have to be at least ' . self::MIN_PASSWORD . ' symbols';
            $response['error_type'] = ErrorTypes::SHORT_PASSWORD;
        } elseif (strlen($password) > self::MAX_PASSWORD) {
            $response['error_message'] = 'Password too long. It have to be ' . self::MAX_PASSWORD . ' symbols max';
            $response['error_type'] = ErrorTypes::LONG_PASSWORD;
        } elseif ($this->usersService->getByUsername($username) !== false) {
            $response['error_message'] = 'User with the same username exists. Please choose other name or log in.';
            $response['error_type'] = ErrorTypes::USERNAME_EXISTS;
        }

        if (isset($response['error_message'])) {
            $response['success'] = false;
            return new JsonResponse($response);
        }
        
        $user = [
            'username' => $username,
            'password' => $password,
            'roles' => UserRoles::ROLE_USER
        ];

        $id = $this->usersService->insert($user);
        $userFromDB = $this->usersService->getOne($id);
        $response['token'] = $userFromDB['token'];

        //Make login subrequest
        $subRequest = Request::create('/' . $app["api.version"] . '/login', 'POST', [
            'username' => $username,
            'password' => $password,
        ], $request->cookies->all(), array(), $request->server->all());


        $response = $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
        return $response;
    }
}
