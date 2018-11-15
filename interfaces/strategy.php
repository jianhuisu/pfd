<?php
/**
 * User: sujianhui
 * Date: 2017-10-20
 * Time: 11:01
 */
namespace interfaces;

abstract class strategy
{
    public $c = null;

    /**
     * 输入原件 返回折后价
     * @param $money
     * @return mixed
     */
    abstract public function cashPay($money);
}