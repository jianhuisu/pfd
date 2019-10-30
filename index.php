<?php
// webshell
var_dump($_GET);exit;
define('WEB_ROOT',__DIR__);
include './vendor/Loader.php';
(new \vendor\Application())->run();


/*
    1 web目录为项目根目录 暴露过多文件 别人可以通过 URL 方式访问该目录中所有文件

        https://www.91chuguokanbing.com/application/database.php

    2 nginx 配置文件中限定 访问文件后缀

        location ~ /\. {

                        deny  all;

                        }

        “access_log off;”不记录访问日志，减轻压力

        统一入口文件

        debug_print_backtrace();

    3 文件上传

    4 php.ini


受制于新的操作环境 以及 键盘布局 ，感觉自身的实力发挥不出来

 * /

