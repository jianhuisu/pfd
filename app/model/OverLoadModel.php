<?php
/**
 * User: sujianhui
 * Date: 2018/11/8
 * Time: 17:01
 */
namespace app\model;

class OverLoadModel
{
    public $obj = null;

    public function __construct()
    {
        $this->obj = new BaseModel();
    }

    public function __call($method,$args)
    {
        $result = call_user_func([$this->obj,$method],$args);
        return $result;
    }

}