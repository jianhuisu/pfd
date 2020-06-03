<?php

class A
{
    public static $num = 0;

    public function __construct()
    {
        self::$num++;
    }

}

new A();
new A();
new A();
echo A::$num;