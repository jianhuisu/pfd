<?php
/**
 * User: sujianhui
 * Date: 2017-12-6
 * Time: 10:34
 */

// 1 实现容器
// 2 使用闭包作为回调
// 3 使用闭包调用类中方法


//  function use 新增的闭包语法中，use 用来捕捉变量到匿名函数内

class Container{

    private $factory;

    public function set($id,$value)
    {
        $this->factory[$id] = $value;
    }

    public function get($id)
    {
        $value = $this->factory[$id];

        // 如果不加括号，返回的仅仅是闭包类 而不是user实例
        return $value();
    }


}

class User
{
    private $username = '';

    public function __construct($name)
    {
        $this->username = $name;
    }

    public function get()
    {
        return $this->username;
    }
}

$con = new Container();
$con->set('user',function (){
    // 此时并不会真正的实例化 user 类
    return new User('sujianhui');
});

var_dump($con->get('user')->get());
echo "\r\n";

// 购物车
class Cart
{
    const PRICE_BUTTER = 1.0;
    const PRICE_MILK = 5.5;

    protected $products;

    public function add($name,$quantity)
    {
        $this->products[$name] = $quantity;
    }

    public function get($name)
    {
        return isset($this->products[$name]) ? $this->products[$name] : false ;
    }

    /**
     * @param $tax
     * @return float
     */
    public function getTotal($tax)
    {
        $total = 0.00;
        $callback = function ($quantity,$name) use ($tax,&$total){

            $itemPrice = constant(__CLASS__.'::PRICE_'.strtoupper($name));
            $total += ($itemPrice * $quantity) * ($tax + 1.0);
        };

        array_walk($this->products,$callback);
        return round($total,2);

    }
}

$c = new Cart();
$c->add('butter',8);
$c->add('milk',1);

echo $c->getTotal(0.1);
echo '<hr>';

class Grid
{
    protected $builder;
    protected $attribute;

    public function __construct(Closure $builler)
    {
        $this->builder = $builler;
    }

    public function addColumn($name, $value)
    {
        $this->attribute[$name] = $value;
        return $this;
    }

    public function build()
    {
        // 这儿回调闭包函数,参数为this
        call_user_func($this->builder, $this);
    }

    public function __toString()
    {
        $this->build();

        $str = '';
        $call = function($val, $key) use(&$str) {
            $str .= "$key=>$val;";
        };
        array_walk($this->attribute, $call);

        return $str;
    }
}

$grid = new Grid(
// 传入闭包函数,带参数
    function($grid) {
        $grid->addColumn('key1', 'val1');
        $grid->addColumn('key2', 'val2');
    }
);

echo $grid;