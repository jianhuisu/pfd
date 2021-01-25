## 缓存


在性能优化的世界里，至上绝招就是在获得同样结果的情况下，减少操作，这就是大名鼎鼎的缓存。缓存无处不在，缓存也是性能优化的杀手锏。

鸟哥在博客中说，提高PHP 7性能的几个tips，第一条就是开启opache. 开启了opcache之后,会带来大幅的性能提升.


运行时缓存：只有针对于特定场景才会触发.


	class my_class {
	    public $id = 123;

	    public function test() {
		echo $this->id;
	    }
	}

	$obj = new my_class;
	$obj->test();
	$obj->test();
	...

这个例子定义了一个类，然后多次调用同一个成员方法，这个成员方法功能很简单：输出一个成员属性，根据前面对成员属性的介绍可以知道其查找过程为：

 1. 首先根据对象找到所属zend_class_entry，
 1. 然后再根据属性名查找zend_class_entry.properties_info哈希表，得到zend_property_info，
 1. 最后根据属性结构的offset定位到属性值的存储位置

那么问题来了：每次执行my_class::test()时难道上面的过程都要完整走一遍吗？

我们再仔细看下这个过程，字面量"id"在"$this->id"此条语句中就是用来索引属性的，不管执行多少次它的任务始终是这个.
**那么有没有一种办法将"id"与查找到的zend_class_entry、zend_property_info.offset建立一种关联关系保存下来**，这样再次执行时直接根据"id"拿到前面关联的这两个数据，从而避免多次重复相同的工作呢？这就是本节将要介绍的内容：运行时缓存。（提供一种快速访问的捷径）

在执行期间，PHP经常需要根据名称去不同的哈希表中查找常量、函数、类、成员方法、成员属性等，
因此PHP提供了一种缓存机制用于缓存根据名称查找到的结果，以便再次执行同一opcode时直接复用上次缓存的值，
无需重复查找，从而提高执行效率。

### 适用场景

开始提到的那个例子中会缓存两个东西：zend_class_entry、zend_property_info.offset，此缓存可以认为是opcode操作的缓存，它只属于"$this->id"此语句的opcode：这样再次执行这条opcode时就直接取出上次缓存的两个值。

所以运行时缓存机制是在同一opcode执行多次的情况下才会生效，特别注意这里的同一opcode指的并不是opcode值相同，而是指内存里的同一份数据。

### 参考资料

https://www.laruence.com/2015/12/04/3086.html
适用opcache需要的注意事项 https://blog.51cto.com/xiaozhagn/2565799

