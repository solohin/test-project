<?php
$app['log.level'] = Monolog\Logger::ERROR;
$app['api.version'] = "v1";

/**
 * SQLite database file
 */
$app['db.options'] = array(
    "driver" => "pdo_mysql",
    "user" => "toptal",
    "password" => "O8z0rD61Z1yQDh38",
    "dbname" => "toptal",
    "host" => "127.0.0.1",
);