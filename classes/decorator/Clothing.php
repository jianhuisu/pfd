<?php
/**
 * User: sujianhui
 * Date: 2017-10-25
 * Time: 10:45
 */
namespace classes\decorator;

class Clothing extends Person
{
    public $people = null;

    public function setPerson($p){
        $this->people = $p;
    }

}