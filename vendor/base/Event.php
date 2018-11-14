<?php
/**
 * User: sujianhui
 * Date: 2018/10/22
 * Time: 16:47
 */
namespace vendor\base;

class Event
{
    private static $_events = [];


    public static function className()
    {
        return get_class();
    }

    public static function on( $name, $handler)
    {
        static::$_events[$name][] = $handler;
    }

    public static function off( $name, $handler)
    {

    }

    public static function trigger($eventName)
    {
        foreach (static::$_events[$eventName] as $eventHandler) {
                call_user_func($eventHandler);
        }

    }

    public static function all()
    {
        var_dump(static::$_events);
    }


}