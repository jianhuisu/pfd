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
        $formatMsg   = date('Y-m-d H:i:s') . ' ' .$msg . "\n";
        // error_log(date('Y-m-d H:i:s') . ' ' .$msg . "\n", 3, $destination );

        if(!file_exists($destination)){
            touch($destination);
        }

        $fd = fopen($destination,'a+');
        fwrite($fd,$formatMsg);
        fclose($fd);

    }


}