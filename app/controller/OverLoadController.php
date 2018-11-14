<?php
/**
 * User: sujianhui
 * Date: 2018/11/8
 * Time: 16:55
 */
namespace app\controller;

use app\model\OverLoadModel;

class OverLoadController extends BaseController
{
    public function actionIndex()
    {
        $obj = new OverLoadModel();
        $count = $obj->display('aaa');

        var_dump("result is :",$count);
    }
}