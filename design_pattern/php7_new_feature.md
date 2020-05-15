## php7 new feature

##### 操作符的改变

<=>
??

##### 内置函数的改变

list new usage
    
    $list = [
        ['sujianhui1','11'],
        ['sujianhui2','12'],
        ['sujianhui3','13'],
    ];
    foreach($list as list($a,$b)){
        echo .$a.$b."\n";
    }

list 还有一种方括号写法

	$arr = [1,2,3];
	[$a,$b,$c] = $arr;

intdiv
    
    intdiv(10,3) <==>  ceil(10/3);
    获取结果的整数位

type declare 类型声明: 

    function (array $list, int $age){
    
    } 

传递错误类型的参数直接提示异常.

##### 其他支持

namespace multiple export class 

throwable 接口的支持

php5.6 之前无法使用 try catch 接口捕获错误，只能捕获异常,
php7   之后可以使用 try catch 捕获错误，    这样可以预防进程直接退出.


	try{
		not_exists_func();
	}catch(Error $e){
		var_dump($e);
	}
	
	set_error_handler(function($e){var_dump($e)}); // 区别于 set_exception_handler()
	

注意: catch Error 才是捕获错误,catch Exception 是捕获异常,注意理解这里面的区别.
 

php7 提供了闭包的call方法,有着更好的性能，将一个闭包函数动态绑定到一个新的对象实例并调用执行该函数。可以动态的绑定，很强大哇

	<?php
	class A{
	    private  $a = 1;
	}	
	
	$f = function (){
	    return $this->num + 1;
	};

	echo $f->call(new A());


##### AST 抽象语法树

方便语法解析,便于以后的使用，以及变种使用

	($a)['b'] = 1;  // => $a['b'] = 1

	

