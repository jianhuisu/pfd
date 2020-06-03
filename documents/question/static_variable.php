<?php

$count = 5;
function get_count()
{
    static $count = 0;
    return $count++;
}
++$count;
get_count();
echo get_count();

// 1
// 如果你回答 2 ，恭喜，你掉入陷阱了。 其实这道题主要考两点，第一点是static静态类型。这种的值永远都是静态的，第一次调用声明等于0，并且自增等于1。第二次调用，1再自增就等于2。但其实这里还有一道陷阱，那就是++a与a++的区别，前++是先自增，后++是先返回值再自增，所以结果等于 1。
