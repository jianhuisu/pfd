<?php


class ParentClass
{
    const MODULE_ID = 0;

    public function getModuleId_byStatic()
    {
        return static::MODULE_ID;
    }

    public function getModuleId_bySelf()
    {
        return self::MODULE_ID;
    }

}

class ChildClass extends ParentClass
{
    const MODULE_ID = 1; // 重写父类中的常量
}

$obj = new ChildClass();

echo $obj->getModuleId_byStatic()."\n";
echo $obj->getModuleId_bySelf()."\n";

//  self : bind when define
//  static : bind when call