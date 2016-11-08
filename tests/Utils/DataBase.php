<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 07/11/16
 * Time: 18:57
 */

namespace Solohin\ToptalExam\Tests\Utils;


class DataBase
{
    const DB_TYPE = 'mysql';

    public static function getTestDBParams()
    {
        if (self::DB_TYPE === 'sqlite') {
//            return [
//                "driver" => "pdo_sqlite",
//                "memory" => true
//            ];
        } else {
            return [
                "driver" => "pdo_mysql",
                "user" => "root",
                "password" => "",
                "dbname" => "testdb",
                "host" => "127.0.0.1",
            ];
        }
    }
}