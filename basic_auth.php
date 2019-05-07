<?php

header("Content-type: text/html; charset=utf-8");

function validate($user, $pass) {
    $users = ['dee'=>'123456', 'admin'=>'admin'];
    if(isset($users[$user]) && $users[$user] === $pass) {
        return true;
    } else {
        return false;
    }
}

if(!validate(@$_SERVER['PHP_AUTH_USER'], @$_SERVER['PHP_AUTH_PW'])) {
    http_response_code(401);
    header('WWW-Authenticate:Basic realm="Pleas input token"'); //对话框显示 http://127.0.0.3 请求用户名和密码。信息为：My website
    echo 'if you want access，please input password';
    exit;
} else {
    var_dump($_SERVER['PHP_AUTH_USER']);
    var_dump($_SERVER['PHP_AUTH_PW']);
    echo "access success\n";
}