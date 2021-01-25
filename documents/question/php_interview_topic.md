## PHP面试真题

##### mysql死锁产生

如何用一句话去描述死锁产生.  mysql死锁是多个事务因竞争锁而造成的一种相互等待的僵局

Apache 的 prefork 模式

##### 真题1 ： 为什么有些 PHP 代码最后不加 代码段结束标记 `?>` ?

这里我们称呼 `?>` 为`代码段结束标记`.
首先 php是一种嵌入式脚本,对php脚本的最原始使用方式是 

	# index.php
	<?php
		echo "<h1>hello world</h1>";
	?>
	
	<table>...</table>
	<?php
		echo "<footer> footer </footer>";
	?>

但是在以下嵌入html的脚本中

	# index.php
	<?php
		echo "<h1>hello world</h1>";
	?>
	
	<table>...</table>
	<?php
		echo "<footer> footer </footer>";
	?>
	// 如果在这里输出几个空格 虽然在编辑器中看不见
	// 但是空格是实际存在的 要占用实际的物理空间
	// 也会被当作正常的字符被输出
	空格1 空格2 空格3 ...
	<?php

		header("content-type:text/html");
	?>

此时,空格字符会先于响应头`content-type:text/html`输出,也就是说http的响应头中掺杂了一些莫名奇妙的空白字符.
在寸土寸金的响应头中，很大概率会导致一些莫名奇妙的问题.所以空白字可能会影响限制我们输出头信息的位置.

另外比较以下这样两个代码段
	
1.php

	#1.php
	<?php
		echo "hello world";
		include "2.php"
	

2.php
	
	#2.php
	<?php
		echo "world ";
	?>
	空格 空格 空格	


此时include操作就会出现问题.

根据php的解释规则,zend虚拟机会忽略`<?php ?>`代码段内的多余空白字符.如果我们没有显式标注 `?>`,
zendVM 会隐式在文件末尾添加一个`?>`.

所以:文件末尾的 PHP 代码段结束标记可以不要,这样不期望的白空格就不会出现在文件末尾,之后仍然可以输出响应标
头。在使用输出缓冲时也很便利.

##### 真题2 php输出语句都有哪些,它们之间的区别?

 - echo         是语法结构/关键字, 没有必要使用括号 无返回值 
 - print        功能与echo相同,同样为语法结构 返回整型1. 效率较echo差.
 - printf       格式化要输出的字符串函数. 
 - sprintf      将格式化后的字符串赋值到一个变量.
 - var_dump     输出变量的内容 类型 长度 ...
 - print_r      函数,以可读性高的格式打印.返回布尔型


##### 真题3 php的函数与语言结构之间有什么区别?


相信大家经常看到对比一些PHP应用中，说用isset() 替换 strlen()，isset比strlen执行速度快等。
原因是isset是语言结构，而strlen是一个函数。还有echo 是个语言结构，不是个函数。

那什么是语言结构呢？它和函数有什么不同吗？ 

 
1 什么是语言结构和函数 

 - 语言结构：就是PHP语言的关键词，语言语法的一部分；它不可以被用户定义或者添加到语言扩展或者库中；它可以有也可以没有变量和返回值。
 - 函数：由代码块组成的，可以复用。从源码的角度来说，也就是基于Zend引擎的基础来实现的，ext拓展库中的函数都是这样实现的。 

2 语言结构为什么比函数快

在PHP中，函数比语言结构多了一层解析器解析过程。这样就能比较好的理解，什么语言结构比函数快了。 
...todo 语法结构的解析发生在语法扫描阶段.scanner bision,而函数则还需要进行解析结构体.

3 语言结构和函数的不同 语言结构比对应功能的函数语言结构在错误处理上比较鲁棒(就是程序的健壮性/有人翻译为重用性?)，
由于是语言关键词，语言结构不能被用做回调函数. 

4 常见的语法结构列表 

 - echo()
 - print()
 - die()
 - isset()
 - unset()
 - include()，注意，include_once()是函数
 - require()，注意，require_once()是函数
 - array()
 - list()
 - empty()

原文链接：https://blog.csdn.net/iteye_7932/article/details/82471419

##### 真题4 值传递与引用传递有什么区别

 - 值传递是将实参的值赋值给形参,形参与实参虽然值相同,但是两个变量指向的内存单元是不同的.两者互不影响.
 - 引用传递是时,实际传递的是 对象的地址,此时形参与实参指向的是同一块存储单元.修改一个就会引发另外一个的改变.

php中基本数据类型(int/string/array)当作实参传递时，默认为值传递方式.对象会使用引用传递方式.
如果想显式控制引用传参,可以使用`&`.


我们在对对象赋值时

`$obj2 = $obj1;`

因为 PHP 使用的是引用传递,所以在执行`$obj2 = $obj1`后,`$obj1`和`$obj2`都是指向同一
个内存区,任何一个对象属性的修改对另外一个对象也是可见的。

`$obj2 = clone $obj1`把`obj1`的整个内存空间复制了一份存放到新的内存空间,并且让`obj2`
指向这个新的内存空间,通过`clone`克隆后.此时对`obj2`的修改对`obj1`是不可见的,因为它们是两个独立的对象。

浅拷贝

	class My_Class {
		public $color;
	}
	$c ="Red";
	$obj1 = new My_Class ();
	$obj1->color =&$c; //这里用的是引用传递
	$obj2 = clone $obj1; //克隆一个新的对象
	$obj2->color="Blue"; //这时,$obj1->color 的值也变成了"Blue"

	?>

深拷贝

		
	class My_Class {
		public $color;
		public function __clone(){
			$this->color = clone $this->color;
		}
	}
	$c ="Red";
	$obj1 = new My_Class ();
	$obj1->color =&$c; //这里用的是引用传递
	$obj2 = clone $obj1; //克隆一个新的对象
	$obj2->color="Blue"; //这时,$obj1->color 的值仍然为"Red"






