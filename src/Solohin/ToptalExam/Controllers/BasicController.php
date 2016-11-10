<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 10/11/16
 * Time: 17:29
 */

namespace Solohin\ToptalExam\Controllers;

use Solohin\ToptalExam\ErrorTypes;
use Solohin\ToptalExam\Services\BaseService;
use Symfony\Component\HttpFoundation\JsonResponse;

class BasicController
{
    protected $service;
    protected $app;

    public function __construct(BaseService $service, \Silex\Application $app)
    {
        $this->service = $service;
        $this->app = $app;
    }

    protected function jsonException(\Exception $e)
    {
        $errorType = $this->service->getLastErrorType();
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
}