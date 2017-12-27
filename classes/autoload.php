<?php
/**
 * User: sujianhui
 * Date: 2017-10-17
 * Time: 14:20
 */
namespace classes;

class Autoload
{
    public static function load($className)
    {

        $realPath = str_replace('\\','/',BASE_DIR.'/'.$className.'.php');
        include "$realPath";

    }
}

