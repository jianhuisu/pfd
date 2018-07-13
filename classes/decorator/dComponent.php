<?php

/**
 * User: sujianhui
 * Date: 2017-10-25
 * Time: 9:40
 */
namespace classes\decorator;

use interfaces\component;

class dComponent extends component
{
    public function operation()
    {
        echo 'wear '.__CLASS__.'<br/>';
    }
}