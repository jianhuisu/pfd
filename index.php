<?php

for($i=0;$i<100;$i++){

    $conn = mysqli_connect("127.0.0.1","sujianhui","xdebug_XDEBUG_5566","im");

    if (!$conn) {
        echo "连接失败！";
        echo mysqli_connect_error();
        exit();
    }
    sleep(5);
    echo "success\n";

}

echo 1;exit;
define('WEB_ROOT',__DIR__);
include './vendor/Loader.php';
(new \vendor\Application())->run();
