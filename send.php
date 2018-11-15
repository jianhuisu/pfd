<?php
/**
 * User: sujianhui
 * Date: 2017-12-4
 * Time: 15:34
 */

ob_start(); //打开缓冲区

$buffer = ini_get('output_buffering');
$buffer = (int)$buffer + 1;
echo str_repeat(' ',1024*64);
//ob_end_flush();
ob_flush();

$i = 1;
while(true){
    echo $i++;
    ob_flush();
    flush();
    sleep(1);
}

?>