<?php

define('WEB_ROOT',__DIR__);
include './vendor/Loader.php';
spl_autoload_register(['\vendor\Loader','autoload']);
set_error_handler('\vendor\ErrorHandle::hand',E_ALL | E_STRICT );
register_shutdown_function(['\vendor\Shutdown','shutdownFunc']);

(new \vendor\Application())->run();

