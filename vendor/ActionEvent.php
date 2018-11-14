<?php
/**
 * User: sujianhui
 * Date: 2018/10/22
 * Time: 17:06
 */
namespace vendor;

use vendor\base\Event;

class ActionEvent extends Event
{
    const EVENT_BEFORE_ACTION = 'beforeAction';
    const EVENT_AFTER_ACTION = 'afterAction';

}