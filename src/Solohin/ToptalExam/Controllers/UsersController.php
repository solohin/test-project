<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 10/11/16
 * Time: 17:23
 */

namespace Solohin\ToptalExam\Controllers;

use Solohin\ToptalExam\ErrorTypes;
use Solohin\ToptalExam\Security\UserRoles;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UsersController extends BasicController
{
    const MIN_NORMAL = 1;
    const DEFAULT_NORMAL = 2000;
    const MAX_NORMAL = 32000;

    private function ICanEditUser($user)
    {
        $me = $this->app['user'];
        $myRole = $this->app['user']->getRoles()[0];

        //I am ok
        if ($me->getId() == $user['id']) {
            return true;
        }

        if ($myRole == UserRoles::ROLE_ADMIN) {
            return true;
        } elseif ($myRole == UserRoles::ROLE_MANAGER) {
            //Manager
            return ($user['roles'][0] == UserRoles::ROLE_USER);
        } else {
            //User can not edit other users
            return false;
        }
    }

    public function removeMe()
    {
        return $this->remove((int)$this->app['user']->getId());
    }

    public function getAll(Request $request)
    {
        $role = $this->app['user']->getRoles()[0];
        if ($role == UserRoles::ROLE_USER) {
            return new JsonResponse([
                'success' => false,
                'error_message' => 'You can not list users',
                'error_type' => ErrorTypes::PERMISSION_DENIED,
            ], 403);
        }
        try {
            $users = $this->service->getAll();
            $users = array_map([$this, 'transformUser'], $users);

            $response = ['success' => true];
            $response['users'] = $users;
            return new JsonResponse($response);
        } catch (\Exception $e) {
            return $this->jsonException($e);
        }
    }

    public function update($id, Request $request)
    {
        /** @var $this ->service UsersService */
        $role = $this->app['user']->getRoles()[0];
        $userToEdit = $this->service->getOne($id);

        //Check edit permissions
        if (!$this->ICanEditUser($userToEdit)) {
            return new JsonResponse([
                'success' => false,
                'error_message' => 'You can not edit this user',
                'error_type' => ErrorTypes::PERMISSION_DENIED,
            ], 403);
        }

        $username = $request->get('username', null);
        $newRole = $request->get('role', null);
        $dailyNormal = $request->get('daily_normal', null);

        //prepare data
        if ($role == UserRoles::ROLE_ADMIN) {
            //its ok
        } elseif ($role == UserRoles::ROLE_MANAGER) {
            $username = null;
            if ($newRole == UserRoles::ROLE_ADMIN) {
                return new JsonResponse([
                    'success' => false,
                    'error_message' => 'You can not set admin role',
                    'error_type' => ErrorTypes::PERMISSION_DENIED,
                ], 403);
            }
        } else {
            $username = null;
            $newRole = null;
        }

        //check data
        if ($dailyNormal !== null) {
            $dailyNormal = (int)$dailyNormal;
        }

        if ($dailyNormal !== null && $dailyNormal > self::MAX_NORMAL || $dailyNormal < self::MIN_NORMAL) {
            $dailyNormal = self::DEFAULT_NORMAL;
        }
        if (!in_array($newRole, [UserRoles::ROLE_MANAGER, UserRoles::ROLE_ADMIN, UserRoles::ROLE_USER])) {
            $newRole = null;
        }

        //check for doubles
        if ($username !== null && $username !== $userToEdit['username']) {
            if ($this->service->getByUsername($username)) {
                return new JsonResponse([
                    'success' => false,
                    'error_message' => 'User with the same username exists',
                    'error_type' => ErrorTypes::USERNAME_EXISTS,
                ], 403);
            }
        }

        //make user object
        $user = [];
        if (!is_null($username)) {
            $user['username'] = $username;
        }
        if (!is_null($newRole)) {
            $user['roles'] = [$newRole];
        }
        if (!is_null($dailyNormal)) {
            $user['daily_normal'] = $dailyNormal;
        }

        //check on changes
        if (
            (!isset($user['roles']) || $user['roles'] === $userToEdit['roles'])
            && (!isset($user['username']) || $user['username'] === $userToEdit)
            && (!isset($user['daily_normal']) || $user['daily_normal'] == $userToEdit['daily_normal'])
        ) {
            //no changes - just return
            return new JsonResponse(['success' => true]);
        }

        try {
            $success = $this->service->update($id, $user);
            $response = ['success' => $success];
            if ($success) {
                return new JsonResponse($response);
            } else {
                $response['error_message'] = 'User not found';
                $response['error_type'] = ErrorTypes::USER_NOT_FOUND;
                return new JsonResponse($response, 404);
            }
        } catch (\Exception $e) {
            return $this->jsonException($e);
        }
    }

    public function updateMe(Request $request)
    {
        return $this->update((int)$this->app['user']->getId(), $request);
    }

    public function remove($id)
    {
        $toDelete = $this->service->getOne($id);

        if (!$toDelete) {
            return new JsonResponse([
                'success' => false,
                'error_message' => 'User ' . $id . ' not found',
                'error_type' => ErrorTypes::USER_NOT_FOUND,
            ], 404);
        }

        if (!$this->ICanEditUser($toDelete)) {
            return new JsonResponse([
                'success' => false,
                'error_message' => 'You can not delete this user',
                'error_type' => ErrorTypes::PERMISSION_DENIED,
            ], 403);
        }

        try {
            $deleted = $this->service->delete($id);
            $response = ['success' => $deleted];
            if ($deleted) {
                return new JsonResponse($response);
            } else {
                $response['error_message'] = 'User not found';
                $response['error_type'] = ErrorTypes::USER_NOT_FOUND;
                return new JsonResponse($response, 500);
            }
        } catch (\Exception $e) {
            return $this->jsonException($e);
        }
    }

    public function getMe()
    {
        return $this->getOne($this->app['user']->getId());
    }

    public function getOne($id)
    {
        $role = $this->app['user']->getRoles()[0];
        if ($role == UserRoles::ROLE_USER && $id != $this->app['user']->getId()) {
            return new JsonResponse([
                'success' => false,
                'error_message' => 'You can not delete this user',
                'error_type' => ErrorTypes::PERMISSION_DENIED,
            ], 403);
        }

        try {
            $user = $this->service->getOne($id);
            $response = ['success' => !!$user];
            if ($user) {
                $response['user'] = $this->transformUser($user);
                return new JsonResponse($response);
            } else {
                $response['error_message'] = 'User not found';
                $response['error_type'] = ErrorTypes::USER_NOT_FOUND;
                return new JsonResponse($response, 404);
            }
        } catch (\Exception $e) {
            return $this->jsonException($e);
        }
    }

    private function transformUser($user)
    {
        $user['role'] = isset($user['roles'][0]) ? $user['roles'][0] : '';
        $user['can_edit'] = $this->ICanEditUser($user);

        unset($user['roles']);
        unset($user['token']);
        unset($user['password']);
        return $user;
    }
}