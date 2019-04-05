<?php
/**
 * User: sujianhui
 * Date: 2018/10/22
 * Time: 18:16
 */
namespace app\controller;

use vendor\base\Event;
use vendor\db\db_mysqli;
use vendor\Log;

class SiteController extends BaseController
{
    public function actionIndex()
    {
        Log::set("can I set log ? ");
       echo $this->render('index',['list' => ['sujianhui','zhaojianwei','zhangwenyuan']]);
    }

}