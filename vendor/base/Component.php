<?php
/**
 * User: sujianhui
 * Date: 2018/11/21
 * Time: 13:41
 */
namespace vendor\base;

/**
 * 组件基类支持 实例级别的事件
 * 不支持通配符匹配
 * Class Component
 * @package vendor\base
 */
class Component extends BaseObj
{
    private $events = [];

    public function on($eventName,$handler)
    {
        $this->events[$eventName][] = $handler;
    }

    public function off($eventName)
    {
        if( isset($this->events[$eventName]) ){
            unset($this->events[$eventName]);
        }
    }

    public function trigger($eventName)
    {
        $eventHandlers = isset($this->events[$eventName]) ? $this->events[$eventName] : [];
        foreach($eventHandlers as $handler)
        {
            // 将事件寄生的对象传递给事件处理器
            call_user_func($handler,$this);
        }
    }

}