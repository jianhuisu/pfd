<?php
namespace classes;

use classes\operate\add;
use classes\operate\minus;

class operateFactory
{
    public static function getOperation($op)
    {
        $operator = null;
        switch($op){
            case '+':
                $operator = new add();
            break;
            case '-':
                $operator = new minus();
                break;
            case '*':
                $operator = new add();
                break;
            case '/':
                $operator = new add();
                break;
        }

        return $operator;
    }

}