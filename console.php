<?php
/**
 * User: sujianhui
 * Date: 2018/11/8
 * Time: 13:33
 */

define('WEB_ROOT',__DIR__);
include './vendor/Loader.php';
spl_autoload_register(['\vendor\Loader','autoload']);
set_error_handler('\vendor\ErrorHandle::hand',E_ALL | E_STRICT );
register_shutdown_function(['\vendor\Shutdown','shutdownFunc']);

if($argc == 1){
    $controllerID = 'MQ'.'Controller';
    $action = 'action'.'pop';
} else if ($argc > 1){
    list($c,$a) = explode("/",$argv[1]);
    $controllerID = ucfirst($c).'Controller';
    $action = 'action'.ucfirst($a);
}

(new \vendor\Application())->run($controllerID,$action,[]);

