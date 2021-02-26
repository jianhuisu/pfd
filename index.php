<?php

//
//
//function checkImageUrlIsAvaiable($url)
//{
//    $url = "http://local.pfd.com/2.png";
//    $url = "https://sfs-public.shangdejigou.cn/sunlands_back_freestudy/gw_banner.png";
//
//
//    if(function_exists('get_headers')){
//
//        if( $headers = get_headers($url,1) ){
//
//            if( !preg_match('/200/',$headers[0]) ){
//                echo 1;exit;
//            }
//            echo 2;exit;
//        }
//
//    }
//
//}
//
//
//
//checkImageUrlIsAvaiable('a');

phpinfo();
exit;
var_dump(ini_get("output_buffer"));
echo "\n";
exit;

define('WEB_ROOT',__DIR__);
include './vendor/Loader.php';
(new \vendor\Application())->run();
