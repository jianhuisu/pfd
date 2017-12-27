<?php
/**
 * User: sujianhui
 * Date: 2017-10-18
 * Time: 8:44
 */
include "./core.php";

// 计算器
$op = \classes\operateFactory::getOperation('-');
$result =  $op->run(1,2);

