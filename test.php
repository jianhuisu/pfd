<?php
/**
 * User: sujianhui
 * Date: 2018/11/9
 * Time: 11:18
 */
abstract class Observer
{
    public function notify(){

    }
}

// 观察者
class ObserverObj extends Observer
{
    public function notify($message)
    {
        echo "receive update nofify ,count value is $message \n";
    }
}

// 被观察对象
class ObservedObj
{
    private $ObserversList = [];
    private $refCount = 1;

    public function __construct()
    {
        $this->registerObserver(new ObserverObj());
    }

    public function generateChange()
    {
        $this->notify($this->refCount++);
    }

    private function notify($message)
    {
        foreach($this->ObserversList as $obj)
        {
            $obj->notify($message);
        }
    }

    public function registerObserver(Observer $observerObj)
    {
        $this->ObserversList[] = $observerObj;
    }

}

$oneObservedObj = new ObservedObj();
$oneObservedObj->generateChange();