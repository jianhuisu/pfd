<?php
namespace vendor;

class ErrorHandle
{
    public static function hand($errorLevel , $errorMsg ,  $errorFile , $errorLine )
    {

        echo "错误编号errno: $errorLevel<br>";
        echo "错误信息errstr: $errorMsg<br>";
        echo "出错文件errfile: $errorFile<br>";
        echo "出错行号errline: $errorLine<br>";
        exit;
    }

}