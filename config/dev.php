<?php
require __DIR__ . '/prod.php';
$app['debug'] = true;
$app['log.level'] = Monolog\Logger::DEBUG;
$app['db.options'] = [
    'driver' => 'pdo_sqlite',
    'path' => realpath(ROOT_PATH . '/app.db'),
];