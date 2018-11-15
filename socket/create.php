<?php
/**
 * User: sujianhui
 * Date: 2017-12-23
 * Time: 15:21
 */
$host = '192.168.32.10';
$port = '19910';
$null = NULL;

//创建tcp socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($socket, 0, $port);

//监听端口
socket_listen($socket);
while(1){
    if((substr(time(),-2,1)%5) == 0){
        $fp = fopen('./../sock.txt','a');
        fwrite($fp,date('Y-m-d H:i:s',time())."\r\n");
        fclose($fp);
    }
}