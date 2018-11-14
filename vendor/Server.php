<?php
namespace vendor;

/**
 * 类内功能尽量依赖原生函数 、减少与其它类的耦合
 * Class Monitor
 * @package vendor
 */
class Server
{

    public static function errorConfig()
    {
        return [
                'display_errors' => ini_get('display_errors'),
                'error_reporting' => ini_get('error_reporting'),
                'log_errors' => ini_get('log_errors'),   // 未开启 为 空 开启后 为 1
                'error_log' => ini_get('error_log'),
        ];
    }

}