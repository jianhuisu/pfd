<?php
/**
 * User: sujianhui
 * Date: 2017-10-30
 * Time: 11:43
 */
namespace classes;

use interfaces\proxyI;

class proxySender implements proxyI
{
    public $girlName = null;

    public function __construct($girlName)
    {
        $this->girlName = $girlName;
    }

    public function giveFlower(){
        echo '送给'.$this->girlName.'鲜花';
    }

    public function giveFood(){

    }

    public function giveGift(){

    }

}