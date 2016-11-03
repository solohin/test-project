<?php

namespace Solohin\ToptalExam\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Solohin\ToptalExam\Services\UsersService;
use Symfony\Component\HttpFoundation\Request;


class LoginController
{
    private $usersService;
    private $app;

    public function __construct(UsersService $service, \Silex\Application $app)
    {
        $this->usersService = $service;
        $this->app = $app;
    }

    public function login(Request $request)
    {
        $response = [];

        $username = $request->request->get('username');
        $password = $request->request->get('password');
        $user = $this->usersService->getByUsername($username);

        if (!isset($user['id'])) {
            //login incorrect
            $response['success'] = false;
            $response['error_message'] = 'Username not found. Please check your username';
        } elseif (!password_verify($password, $user['password'])) {
            //password incorrect
            $response['success'] = false;
            $response['error_message'] = 'Password incorrect. Please check your password.';
        } else {
            //all correct
            $response['success'] = true;
            $response['token'] = $this->getNewToken($user['id']);
        }
        return new JsonResponse($response);
    }

    private function getNewToken($userId)
    {
        do {
            $randomStr = rand(1000000, 100000000) . rand(1000000, 100000000);
            $token = password_hash($randomStr, PASSWORD_DEFAULT);
        } while ($this->usersService->isTokenExists($token));
        $user['token'] = $token;
        $this->usersService->update($userId, ['token' => $token]);
        return $token;
    }
}
