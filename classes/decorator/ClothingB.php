<?php
/**
 * User: sujianhui
 * Date: 2017-10-25
 * Time: 10:47
 */

namespace classes\decorator;

use classes\decorator\Clothing;

class ClothingB extends Clothing
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