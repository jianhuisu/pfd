<?php
namespace vendor;

spl_autoload_register(['\vendor\Loader','autoload']);

class Loader
{
    public static function autoload($className)
    {
        // \vendor\Loader 传递到此函数 变为 vendor\Loader  省略了开始出的 \
        require WEB_ROOT.'/'.str_replace('\\','/',$className).'.php';
    }

}