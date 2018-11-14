<?php
/**
 * User: sujianhui
 * Date: 2018/11/8
 * Time: 14:51
 */
namespace app\controller;

class SecKillController extends BaseController
{
    /**
     * 秒杀 流量削峰
     * @return string
     */
    public function actionIndex()
    {
        $length = 10;
        if($length > 10){
            return "fail, queue is full";
        } else {
            return "success";
        }

    }

}