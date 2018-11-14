<?php
/**
 * User: sujianhui
 * Date: 2018/11/8
 * Time: 17:00
 */
namespace app\model;

class BaseModel
{
    public function display($args)
    {
        var_dump($args);
        return 1;
    }
}