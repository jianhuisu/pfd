<?php
/**
 * User: sujianhui
 * Date: 2017-10-25
 * Time: 9:26
 */
namespace classes\decorator;

use interfaces\component;

class Decorator extends component
{
    public $component = null;

    public function setComponent($c)
    {
        $this->component = $c;
    }

    public function operation()
    {
        $this->component->operation();
    }

}