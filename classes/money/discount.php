<?php
/**
 * User: sujianhui
 * Date: 2017-10-20
 * Time: 11:13
 */
namespace classes\money;

use interfaces\strategy;

/**
 * 折扣算法类
 * Class discount
 * @package classes\money
 */
class discount extends strategy
{
    public $discountPercent;

    public function __construct($discountPercent)
    {
        $this->discountPercent = $discountPercent;
    }

    public function cashPay($money)
    {
        // TODO: Implement cashPay() method.
        return $money*$this->discountPercent;

    }

}