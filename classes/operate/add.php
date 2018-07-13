<?php
/**
 * User: sujianhui
 * Date: 2017-10-17
 * Time: 10:56
 */
namespace classes\operate;

use interfaces\operate;

class add implements operate
{
    public function run($op1,$op2)
    {
        return ($op1 + $op2);
    }
}