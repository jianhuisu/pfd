<?php
/**
 * User: sujianhui
 * Date: 2017-10-20
 * Time: 11:13
 */
namespace classes\money;

use interfaces\strategy;

/**
 * 正常算法类
 * Class normal
 * @package classes\money
 */
class normal extends strategy
{
    public function cashPay($money)
    {
        // TODO: Implement cashPay() method.
        return $money;
    }

}