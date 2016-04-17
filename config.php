<?php
/**
 * Created by PhpStorm.
 * User: Michal
 * Date: 08.03.2016
 * Time: 21:52
 */

define("ROOT", __DIR__ . "/");

ini_set('display_errors', 'On');
error_reporting(E_ALL);

class Config
{

    private static $config = [
        
    ];

    public static function get($key, $default = null)
    {
        list($first, $second) = explode('.', $key);

        if (isset(self::$config[$first][$second])) {
            return self::$config[$first][$second];
        }

        return $default;
    }
}