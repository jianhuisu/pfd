<?php

while (true) {
    echo time();
    ini_set('default_socket_timeout', -1);  //不超时
    $redis = new Redis();
    $redis->connect('127.0.01', 6379, 3600);
    //$redis->auth('123456'); //设置密码
    $result = $redis->subscribe(['test'], 'callback');
    print_r($result);
    sleep(0.1);
}

function callback($instance, $channelName, $message)
{
    print_r($message);
}
