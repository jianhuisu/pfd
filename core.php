<?php
/**
 * User: sujianhui
 * Date: 2017-10-17
 * Time: 10:51
 * 封装 将业务逻辑与界面逻辑分离，分别封装，只有降低业务逻辑与界面逻辑之间的耦合度，才能达到易维护 、易扩展
 * 在 编写计算器程序中 业务逻辑 是指 计算部分  界面逻辑 是指 数据的收集与显示部分
 *
 * 演变的过程非常重要
 */

define('BASE_DIR',__DIR__);
include './classes/autoload.php';
spl_autoload_register('\classes\autoload::load');





