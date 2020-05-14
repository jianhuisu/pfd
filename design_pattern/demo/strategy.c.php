// 策略模式
// 构建相同的上下文，独立中间策略算法

<?php

echo "我钓了一条鱼，应该怎么吃呢？ \n";

$context = new Context(new StrategyA());
$context->RunMethod();

$context = new Context(new StrategyB());
$context->RunMethod();

$context = new Context(new StrategyC());
$context->RunMethod();

$context = new Context(new StrategyD());
$context->RunMethod();

echo "success \n";


class Context{

    public $strategy;

    //构造方法接收具体对象
    public function __construct(Strategy $s)
    {
        $this->strategy = $s;
    }

    //调用方法
    public function  RunMethod()
    {
        echo $this->strategy->AlgorithmInterface()."\n";
    }
}

abstract class Strategy{
    abstract public function AlgorithmInterface();
}

class StrategyA extends Strategy
{
    public function AlgorithmInterface(){
        return __CLASS__.": 清蒸";
    }
}

class StrategyB extends Strategy
{
    public function AlgorithmInterface(){
        return __CLASS__.": 铁锅炖";
    }
}

class StrategyC extends Strategy
{
    public function AlgorithmInterface(){
        return __CLASS__.": 暴炒";
    }
}

class StrategyD extends Strategy
{
    public function AlgorithmInterface(){
        return __CLASS__.": 油炸";
    }
}