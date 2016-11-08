<?php

namespace Solohin\ToptalExam\Controllers;

use Solohin\ToptalExam\ErrorTypes;
use Solohin\ToptalExam\Security\UserRoles;
use Symfony\Component\HttpFoundation\JsonResponse;
use Solohin\ToptalExam\Services\NotesService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;


class NotesController
{
    private $notesService;
    private $app;

    public function __construct(NotesService $service, \Silex\Application $app)
    {
        $this->notesService = $service;
        $this->app = $app;
    }

    public function getList(Request $request)
    {
        $response = [
            'success' => true
        ];
        $notes = $this->notesService->getAll(
            $request->get('user_id'),
            $request->get('from_date'),
            $request->get('to_date'),
            $request->get('from_time'),
            $request->get('to_time'),
            $request->get('page', 1)
        );
        $response['notes'] = $notes;
        return new JsonResponse($response);
    }

    public function getOne($id)
    {
        throw new \Exception('Method not implemented yet');
    }

    public function add(Request $request)
    {
        $userId = intval($request->get('user_id'));

        $role = $this->app['user']->getRoles()[0];

        if ($role === UserRoles::ROLE_MANAGER) {
            return new JsonResponse([
                'success' => false,
                'error_message' => 'Manager can nott add notes',
                'error_type' => ErrorTypes::PERMISSION_DENIED,
            ], 403);
        } elseif ($role === UserRoles::ROLE_ADMIN) {
            if (!$userId) {
                return new JsonResponse([
                    'success' => false,
                    'error_message' => 'User ID can not be empty for user',
                    'error_type' => ErrorTypes::EMPTY_USER_ID,
                ], 400);
            }
        } else {
            $userId = $this->app['user']->getID();
        }

        $response = [
            'success' => true
        ];
        $note = [
            'text' => $request->get('text'),
            'calories' => $request->get('calories'),
            'user_id' => $userId,
            'time' => $request->get('time'),
            'date' => $request->get('date'),
        ];
        try {
            $response['id'] = $this->notesService->insert($note);
        } catch (\Exception $e) {
            return $this->jsonException($e);
        }
        return new JsonResponse($response);
    }

    private function jsonException(\Exception $e)
    {
        $errorType = $this->notesService->getLastErrorType();
        $errorMessage = $e->getMessage();
        $errorCode = $e->getCode();

        if ($errorCode < 200) {
            $errorCode = 500;
        }

        if (empty($errorType)) {
            $errorType = ErrorTypes::INTERNAL_ERROR;
            $errorMessage = 'Something goes wrong. Please email screenshot to solohin.i@gmail.com';

            $this->app['monolog']->addError($e->getMessage());
            $this->app['monolog']->addError($e->getTraceAsString());
        }
        return new JsonResponse([
            'error_message' => $errorMessage,
            'error_type' => $errorType,
            'success' => false
        ], $errorCode);
    }

    public function remove($id)
    {
        throw new \Exception('Method not implemented yet');
    }

    public function update($id, Request $request)
    {
        throw new \Exception('Method not implemented yet');
    }

    public function delete($id)
    {
        throw new \Exception('Method not implemented yet');
    }
}
