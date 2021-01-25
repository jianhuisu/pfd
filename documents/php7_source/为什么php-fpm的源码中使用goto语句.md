## 为什么要在php-fpm源码中使用非常具有争议性的goto语句

goto语句受到很多人的诟病.但是为什么要在php-fpm源码中使用非常具有争议性的goto语句呢？
我是不是可以这样理解:函数跳转并不能完全替代goto语句.

阅读过linux内核代码的同学应该注意到，linux内核代码里面其实有不少地方用了goto语句，但是你会发现，它的使用非常谨慎.

先看一下php源码

	```php
	int fpm_run(int *max_requests)
	{
	    struct fpm_worker_pool_s *wp;
	    for (wp = fpm_worker_all_pools; wp; wp = wp->next) {
		//调用fpm_children_make() fork子进程
		is_parent = fpm_children_create_initial(wp);
		
		if (!is_parent) {
		    goto run_child;
		}
	    }
	    //master进程将进入event循环，不再往下走
	    fpm_event_loop(0);

	run_child: //只有worker进程会到这里

	    *max_requests = fpm_globals.max_requests;
	    return fpm_globals.listening_socket; //返回监听的套接字
	}
	```	

## handler

**handler为每条opcode对应的C语言编写的 处理过程** ，所有opcode对应的处理过程定义在zend_vm_def.h中，值得注意的是这个文件并不是编译时用到的，因为opcode的 处理过程 有三种不同的提供形式：

 1. `CALL`
 1. `SWITCH`
 1. `GOTO`

默认方式为CALL，这个是什么意思呢？
每个opcode都代表了一些特定的处理操作，这个东西怎么提供呢？

 - `CALL` :一种是把每种opcode负责的工作封装成一个function，然后执行器循环执行即可，这就是CALL模式的工作方式；
 - `GOTO` :另外一种是把所有opcode的处理方式通过C语言里面的label标签区分开，然后执行器执行的时候goto到相应的位置处理，这就是GOTO模式的工作方式；
 - `SWITCH` 最后还有一种方式是把所有的处理方式写到一个switch下，然后通过case不同的opcode执行具体的操作，这就是SWITCH模式的工作方式。

**三种模式效率是不同的，GOTO最快**.

### 参考资料

https://github.com/jianhuisu/php7-internal/blob/master/3/zend_compile_opcode.md
