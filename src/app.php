<?php

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Solohin\ToptalExam\Database\SchemaCreator;
use Solohin\ToptalExam\Database\SchemaManager;
use Solohin\ToptalExam\ErrorTypes;
use Solohin\ToptalExam\Security\TokenAuthenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Solohin\ToptalExam\ServicesLoader;
use Solohin\ToptalExam\RoutesLoader;
use Carbon\Carbon;
use Silex\Provider\VarDumperServiceProvider;
use Solohin\ToptalExam\SecurityLoader;

/** @var $app Silex\Application run */

//Accepting JSON
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

//Database
$app->register(new DoctrineServiceProvider(), array(
    "db.options" => $app["db.options"]
));

//Token auth
$app->register(new MonologServiceProvider(), array(
    "monolog.logfile" => ROOT_PATH . "/logs/" . Carbon::now()->format("Y-m-d") . ".log",
    "monolog.level" => $app["log.level"],
    "monolog.name" => "application"
));

//Token auth
$app['app.token_authenticator'] = function ($app) {
    return new TokenAuthenticator($app['security.encoder_factory']);
};

$app['schema_manager'] = function ($app) {
    return new SchemaManager($app['db']);
};

//Debug
if ($app['debug']) {
    $app->register(new VarDumperServiceProvider());
    //Create tables
    $app['schema_manager']->createTables();
}

//load services
$app->register(new ServiceControllerServiceProvider());
$servicesLoader = new ServicesLoader($app);
$servicesLoader->bindServicesIntoContainer();

//Routes
$routesLoader = new RoutesLoader($app);
$routesLoader->bindRoutesToControllers();

//Security
$securityLoader = new SecurityLoader($app);
$securityLoader->initTokenSecurity();

//Fallback
$app->match("/{anything}", function () use ($app) {
    return new JsonResponse([
        'success' => false,
        'error_message' => 'Method not found',
        'error_type' => ErrorTypes::METHOD_NOT_FOUND
    ], 404);
})->assert('anything', '.*');

//Handle Exceptions
$app->error(function (Exception $e, $code) use ($app) {
    $app['monolog']->addError($e->getMessage());
    $app['monolog']->addError($e->getTraceAsString());
    return new JsonResponse(array("statusCode" => $code, "message" => $e->getMessage(), "stacktrace" => $e->getTraceAsString()));
});

return $app;
