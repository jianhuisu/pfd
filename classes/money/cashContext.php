<?php
/**
 * User: sujianhui
 * Date: 2017-10-20
 * Time: 11:13
 */
namespace classes\money;

class cashContext
{
    // 工厂模式不一定非得是一个单独的类 一个函数也可以实现简单工厂的功能
    public function cou($type,$money)
    {
        switch($type){
            case 'discount':
            $obj =  new discount(0.8);
            return $obj->cashPay($money);
            break;
            case 'rebate':
            $obj =  new rebate(500,300);
            return $obj->cashPay($money);
            break;
            case 'normal':
            $obj =  new normal();
            return $obj->cashPay($money);
            break;
        }
    }

}