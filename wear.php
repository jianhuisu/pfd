<?php
/**
 * 装饰器模式 实现对自己动态的添加功能
 *
 * 把自己当做变量设置为 装饰器类的 成员变量
 *
 * 装饰器类中
 * 调用 父类方法 $this->component->operator()
 * 然后调用自定义方法
 */

include './core.php';

$dc = new \classes\decorator\dComponent();
$A = new \classes\decorator\DecoratorA();
$B = new \classes\decorator\DecoratorB();

$A->setComponent($dc);
$A->operation();
echo '<hr/>';
$B->setComponent($A);
$B->operation();