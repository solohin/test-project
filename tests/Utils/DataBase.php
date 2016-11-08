<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 07/11/16
 * Time: 18:57
 */

namespace Solohin\ToptalExam\Tests\Utils;


class DataBase{
    public static function getTestDBParams(){
//        return [
//            "db.options" => [
//                "driver" => "pdo_sqlite",
//                "memory" => true
//            ],
//        ];
        return [
            "db.options" => [
                "driver" => "pdo_mysql",
                "user" => "root",
                "password" => "",
                "dbname" => "test",
                "host" => "127.0.0.1",
            ],
        ];
    }
}