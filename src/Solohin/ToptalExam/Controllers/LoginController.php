<?php

namespace Solohin\ToptalExam\Controllers;

use Solohin\ToptalExam\ErrorTypes;
use Symfony\Component\HttpFoundation\JsonResponse;
use Solohin\ToptalExam\Services\UsersService;
use Symfony\Component\HttpFoundation\Request;


class LoginController
{
    private $usersService;

    public function __construct(UsersService $service)
    {
        $this->usersService = $service;
    }

    public function login(Request $request)
    {
        $response = [];

        $username = trim($request->request->get('username'));
        $password = trim($request->request->get('password'));
        $user = $this->usersService->getByUsername($username);

        if (!isset($user['id'])) {
            //login incorrect
            $response['success'] = false;
            $response['error_message'] = 'Username not found. Please check your username';
            $response['error_type'] = ErrorTypes::WRONG_USERNAME;
        } elseif (!password_verify($password, $user['password'])) {
            //password incorrect
            $response['success'] = false;
            $response['error_message'] = 'Password incorrect. Please check your password.';
            $response['error_type'] = ErrorTypes::WRONG_PASSWORD;
        } else {
            //all correct
            $response['success'] = true;
            $response['token'] = $user['token'];
        }
        return new JsonResponse($response);
    }
}
