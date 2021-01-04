<?php



phpinfo();
exit;
var_dump(ini_get("output_buffer"));
echo "\n";
exit;

define('WEB_ROOT',__DIR__);
include './vendor/Loader.php';
(new \vendor\Application())->run();
