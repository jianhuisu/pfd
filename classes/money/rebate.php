<?php
/**
 * User: sujianhui
 * Date: 2017-10-20
 * Time: 11:13
 */
namespace classes\money;

use interfaces\strategy;

/**
 * 返利类
 * Class rebate
 * @package classes\money
 */
class rebate extends strategy
{
    public $critical;
    public $reduce;

    public function __construct($critical,$reduce)
    {
        $this->critical = $critical;
        $this->reduce = $reduce;
    }

    public function cashPay($money)
    {
        // TODO: Implement cashPay() method.
        return ($money > $this->critical) ? ($money - $this->reduce) : $money ;
    }

}