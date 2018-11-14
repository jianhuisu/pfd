<?php
/**
 * User: sujianhui
 * Date: 2018/10/22
 * Time: 18:16
 */
namespace app\controller;

use vendor\ActionEvent;
use vendor\base\Event;

class BaseController
{

    public function __construct()
    {
        Event::on(ActionEvent::EVENT_BEFORE_ACTION,[$this,ActionEvent::EVENT_BEFORE_ACTION]);
        Event::on(ActionEvent::EVENT_AFTER_ACTION,[$this,ActionEvent::EVENT_AFTER_ACTION]);
    }

    public function beforeAction()
    {

    }

    public function afterAction()
    {

    }

}