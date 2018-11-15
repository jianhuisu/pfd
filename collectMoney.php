<?php
/**
 * User: sujianhui
 * Date: 2017-10-20
 * Time: 8:40
 */
include './core.php';

$cm = new classes\money\cashContext();
$cm->cou('discount',500);

/**
 * 实现一个功能 引用两个类设置参数  比 引用一个类设置参数 耦合度高
 *
 * 应用环境：不同条件应用不同的业务规则，均可以考虑使用策略（strategy) 来封装业务规则(算法)
 * 策略模式封装了变化
 */
