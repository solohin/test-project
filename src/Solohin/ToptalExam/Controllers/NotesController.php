<?php

namespace Solohin\ToptalExam\Controllers;

use Solohin\ToptalExam\ErrorTypes;
use Solohin\ToptalExam\Security\UserRoles;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class NotesController extends BasicController
{

    public function getList(Request $request)
    {
        $userId = $request->get('user_id', null);
        $role = $this->app['user']->getRoles()[0];

        if ($role === UserRoles::ROLE_MANAGER) {
            return new JsonResponse([
                'success' => false,
                'error_message' => 'Manager can not read notes',
                'error_type' => ErrorTypes::PERMISSION_DENIED,
            ], 403);
        } elseif ($role === UserRoles::ROLE_ADMIN) {
            //its ok
        } else {
            $userId = $this->app['user']->getID();
        }

        try {
            $notes = $this->service->getAll(
                $userId,
                $request->get('from_date'),
                $request->get('to_date'),
                $request->get('from_time'),
                $request->get('to_time'),
                $request->get('page', 1)
            );
            $response = ['success' => true];
            $response['notes'] = $notes;
            $response['total_calories'] = array_sum(array_column($notes, 'calories'));
            return new JsonResponse($response);
        } catch (\Exception $e) {
            return $this->jsonException($e);
        }
    }

    public function getOne($id)
    {
        $role = $this->app['user']->getRoles()[0];

        if ($role === UserRoles::ROLE_MANAGER) {
            return new JsonResponse([
                'success' => false,
                'error_message' => 'Manager can not read notes',
                'error_type' => ErrorTypes::PERMISSION_DENIED,
            ], 403);
        } elseif ($role === UserRoles::ROLE_ADMIN) {
            $userId = null;
        } else {
            $userId = $this->app['user']->getID();
        }

        try {
            $note = $this->service->getOne($id, $userId);
            $response = ['success' => !!$note];
            if ($note) {
                $response['note'] = $note;
                return new JsonResponse($response);
            } else {
                $response['error_message'] = 'Note not found';
                $response['error_type'] = ErrorTypes::NOTE_NOT_FOUND;
                return new JsonResponse($response, 404);
            }
        } catch (\Exception $e) {
            return $this->jsonException($e);
        }
    }

    public function add(Request $request)
    {
        $userId = intval($request->get('user_id'));
        $role = $this->app['user']->getRoles()[0];

        if ($role === UserRoles::ROLE_MANAGER) {
            return new JsonResponse([
                'success' => false,
                'error_message' => 'Manager can not add notes',
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
            $response['id'] = $this->service->insert($note);
        } catch (\Exception $e) {
            return $this->jsonException($e);
        }
        return new JsonResponse($response);
    }

    public function update($id, Request $request)
    {
        $role = $this->app['user']->getRoles()[0];

        if ($role === UserRoles::ROLE_MANAGER) {
            return new JsonResponse([
                'success' => false,
                'error_message' => 'Manager can not update notes',
                'error_type' => ErrorTypes::PERMISSION_DENIED,
            ], 403);
        } elseif ($role === UserRoles::ROLE_ADMIN) {
            $userId = $request->get('user_id');
            if (!is_null($userId)) {
                $userId = (int)$userId;
            }
            $userIdFilter = null;
        } else {
            $userId = null;
            $userIdFilter = $this->app['user']->getID();
        }

        $note = [
            'text' => $request->get('text'),
            'calories' => $request->get('calories'),
            'user_id' => $userId,
            'time' => $request->get('time'),
            'date' => $request->get('date'),
        ];

        foreach ($note as $key => $value) {
            if (is_null($value)) {
                unset($note[$key]);
            }
        }
        if (empty($note)) {
            return new JsonResponse([
                'success' => false,
                'error_message' => 'Update fields must be filled.',
                'error_type' => ErrorTypes::EMPTY_PARAMETERS,
            ], 400);
        }

        try {
            $success = $this->service->update($id, $note, $userIdFilter);
            $response = ['success' => $success];
            if ($success) {
                return new JsonResponse($response);
            } else {
                $response['error_message'] = 'Note not found';
                $response['error_type'] = ErrorTypes::NOTE_NOT_FOUND;
                return new JsonResponse($response, 404);
            }
        } catch (\Exception $e) {
            return $this->jsonException($e);
        }
    }

    public function remove($id)
    {
        $role = $this->app['user']->getRoles()[0];

        if ($role === UserRoles::ROLE_MANAGER) {
            return new JsonResponse([
                'success' => false,
                'error_message' => 'Manager can not delete notes',
                'error_type' => ErrorTypes::PERMISSION_DENIED,
            ], 403);
        } elseif ($role === UserRoles::ROLE_ADMIN) {
            $userId = null;
        } else {
            $userId = $this->app['user']->getID();
        }

        try {
            $deleted = $this->service->delete($id, $userId);
            $response = ['success' => $deleted];
            if ($deleted) {
                return new JsonResponse($response);
            } else {
                $response['error_message'] = 'Note not found';
                $response['error_type'] = ErrorTypes::NOTE_NOT_FOUND;
                return new JsonResponse($response, 404);
            }
        } catch (\Exception $e) {
            return $this->jsonException($e);
        }
    }
}
