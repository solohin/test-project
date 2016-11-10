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
use Symfony\Component\HttpFoundation\JsonResponse;
use Solohin\ToptalExam\Services\Service;


class UsersController extends BasicController
{
    const MIN_NORMAL = 1;
    const DEFAULT_NORMAL = 2000;
    const MAX_NORMAL = 32000;


    public function remove($id)
    {
        $role = $this->app['user']->getRoles()[0];
        $this->service->delete($id);
    }

    public function getOne($id)
    {
        try {
            $user = $this->service->getOne($id);
            $response = ['success' => !!$user];
            if ($user) {
                $response['user'] = $user;
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
}