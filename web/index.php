<?php
$runAppDirectly = (count(get_included_files()) == 1);

require_once __DIR__ . '/../vendor/autoload.php';

//Check if defined in case of call from PHPUnit
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

$app = new Silex\Application();

//require __DIR__ . '/../config/dev.php';
require __DIR__ . '/../config/prod.php';
require __DIR__ . '/../src/app.php';

if ($runAppDirectly) {
    $app->run();
}

return $app;