<?php
require __DIR__ . '/prod.php';
$app['debug'] = true;
$app['log.level'] = Monolog\Logger::DEBUG;
$app['db.options'] = \Solohin\ToptalExam\Tests\Utils\DataBase::getTestDBParams();
