<?php
/**
 * User: sujianhui
 * Date: 2018/9/19
 * Time: 19:34
 */
namespace vendor;


class Log
{

    public static function set($msg)
    {
        $runtime = WEB_ROOT.'/runtime/logs/';
        $file = date('Ymd',time()).'.log';
        $destination = $runtime.$file;
        echo $msg."\n";
        error_log(date('Y-m-d H:i:s') . ' ' .$msg . "\n", 3, $destination );

    }


}