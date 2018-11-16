<?php
/**
 * User: sujianhui
 * Date: 2018/10/22
 * Time: 18:16
 */
namespace app\controller;

use vendor\base\Event;
use vendor\db\db_mysqli;

class SiteController extends BaseController
{
    public function actionIndex()
    {
       echo $this->render('index',['list' => ['sujianhui','zhaojianwei','zhangwenyuan']]);
    }

}