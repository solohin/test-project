<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 08/11/16
 * Time: 16:37
 */

namespace Solohin\ToptalExam;

class Debug
{
    private static $logger;


    public static function setLogger($logger)
    {
        self::$logger = $logger;
    }

    public static function debug($message)
    {
        if (is_array($message) || is_object($message)) {
            $tokens = [];
            foreach ($message as $key => $item) {
                $type = gettype($item);
                if (is_array($item)) {
                    $item = 'Array of ' . implode(',', array_keys($item));
                }
                $tokens[] = $key . '=' . $item . '(' . $type . ')';
            }
            $message = implode(',', $tokens);
        }
        if (self::$logger instanceof \Psr\Log\LoggerInterface) {
            self::$logger->addDebug($message);
        }
    }
}