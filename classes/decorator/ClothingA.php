<?php
/**
 * User: sujianhui
 * Date: 2017-10-25
 * Time: 10:47
 */

namespace classes\decorator;


class ClothingA extends Clothing
{
    public function wear()
    {
        $this->people->wear();
        $this->selfWear();
    }

    public function selfWear(){
        echo 'wear '.__CLASS__.'<br/>';
    }
}