<?php

$a = '0';
$b = 0;
$c = '';
$d = NULL;

var_dump(empty($a));
var_dump(empty($b));
var_dump(empty($c));
var_dump(empty($d));
var_dump(empty(false));
var_dump(empty(true));
echo "-------\n";
var_dump(isset($a));
var_dump(isset($b));
var_dump(isset($c));
var_dump(isset($d));
// var_dump(isset(false)); error    非法调用方式
// var_dump(isset(true));  error    非法调用方式
echo "------\n";
var_dump(is_null($a));
var_dump(is_null($b));
var_dump(is_null($c));
var_dump(is_null($d));
var_dump(is_null(true));
var_dump(is_null(false));
echo "------\n";
var_dump( '0' === 0);
var_dump( '0' == 0);
var_dump( '0abc' == 0);


exit;


