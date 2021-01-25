
知其然知其所以然:如果你不知道为什么这样处理，者只能说明你了解的还不够透彻

性能加速 开启 opcache. 

我是不相信一剑破万法的,任何方案都有两面性(天下无功 唯快不破 这句话除外).所以任何方案都有它适用的场景.
在合适的时间，合适的地点做合适的事 这才是生存之道.

虽然opcache能带来巨大的性能提升，但是在我的实际工作中很少有项目开启这个东西.我猜多半是大家不熟悉这个东西,出了问题不具备解决问题的能力.大家更加习惯于默认的配置,舒适区中使用习惯.
或者使用redis提升性能来弥补php方面的损耗.

Nginx 的worker进程可以同时响应多个请求  这跟编程模型有关系
也就是说fpm的子进程同时只能响应一个请求

子进程的处理非常简单，它在启动后阻塞在accept上，有请求到达后开始读取请求数据，读取完成后开始处理然后再返回，在这期间是不会接收其它请求的，也就是说fpm的子进程同时只能响应一个请求，只有把这个请求处理完成后才会accept下一个请求，

这一点与nginx的事件驱动有很大的区别，nginx的子进程通过epoll管理套接字，如果一个请求数据还未发送完成则会处理下一个请求，即一个进程会同时连接多个请求，它是非阻塞的模型，只处理活跃的套接字。

fpm可以同时监听多个端口，每个端口对应一个worker pool，而每个pool下对应多个worker进程，类似nginx中server概念。（一个server.conf 可以监听一个端口）


在fork后worker进程返回了监听的套接字继续main()后面的处理，而master将永远阻塞在fpm_event_loop()


请求结束了，并不代表刚刚处理这个请求的worker进程就要销毁了.
请求结束可能仅仅说明处理这个请求时所申请的资源可能需要全部释放掉.


第一阶段 用别人的轮子
第二阶段 写自己的轮子别人用.

（每个阶段都有一个清晰的钩子. 或者说一个清晰的节点. 降低程序的复杂度. (管道+模块 处理方式) 每个模块的输出都是下一个模块的输入）

对于worker进程而言,php脚本的内容就是一段字符串而已. 只不过这段字符串很长很长. 

PHP是解析型高级语言，事实上从Zend内核的角度来看PHP就是一个普通的C程序，它有main函数，我们写的PHP代码是这个程序的输入，
然后经过内核的处理输出结果，内核将PHP代码"翻译"为C程序可识别的过程就是PHP的编译。

那么这个"翻译"过程具体都有哪些操作呢？

C程序在编译时将一行行代码编译为机器码，每一个操作都认为是一条机器指令，这些指令写入到编译后的二进制程序中，执行的时候将二进制程序load进相应的内存区域(常量区、数据区、代码区)、分配运行栈，然后从代码区起始位置开始执行，这是C程序编译、执行的简单过程。

同样，PHP的编译与普通的C程序类似，只是PHP代码没有编译成机器码，而是解析成了若干条opcode数组，每条opcode就是C里面普通的struct，含义对应C程序的机器指令，执行的过程就是引擎依次执行opcode，比如我们在PHP里定义一个变量:$a = 123;，最终到内核里执行就是malloc一块内存，然后把值写进去。

所以PHP的解析过程任务就是将PHP代码转化为opcode数组，代码里的所有信息都保存在opcode中，然后将opcode数组交给zend引擎执行，opcode就是内核具体执行的命令，比如赋值、加减操作、函数调用等，每一条opcode都对应一个处理handle，这些handler是提前定义好的C函数。

从PHP代码到opcode是怎么实现的？最容易想到的方式就是正则匹配，当然过程没有这么简单。PHP编译过程包括词法分析、语法分析，使用re2c、bison完成，旧的PHP版本直接生成了opcode，PHP7新增了抽象语法树（AST），在语法分析阶段生成AST，然后再生成opcode数组。


PHP主脚本会生成一个zend_op_array，每个function也会编译为独立的zend_op_array，所以从二进制程序的角度看zend_op_array包含着当前作用域下的所有堆栈信息，函数调用实际就是不同zend_op_array间的切换。


index.php 统领三军 ，呼叫a.php出列.

	ZEND_API zend_op_array *compile_file(zend_file_handle *file_handle, int type)
	{
	    zend_op_array *op_array = NULL; //编译出的opcodes
	    ...

	    if (open_file_for_scanning(file_handle)==FAILURE) {//文件打开失败
		...
	    } else {
		zend_bool original_in_compilation = CG(in_compilation);
		CG(in_compilation) = 1;

		CG(ast) = NULL;
		CG(ast_arena) = zend_arena_create(1024 * 32);
		if (!zendparse()) { //语法解析
		    zval retval_zv;
		    zend_file_context original_file_context; //保存原来的zend_file_context
		    zend_oparray_context original_oparray_context; //保存原来的zend_oparray_context，编译期间用于记录当前zend_op_array的opcodes、vars等数组的总大小
		    zend_op_array *original_active_op_array = CG(active_op_array);
		    op_array = emalloc(sizeof(zend_op_array)); //分配zend_op_array结构
		    init_op_array(op_array, ZEND_USER_FUNCTION, INITIAL_OP_ARRAY_SIZE);//初始化op_array
		    CG(active_op_array) = op_array; //将当前正在编译op_array指向当前
		    ZVAL_LONG(&retval_zv, 1);

		    if (zend_ast_process) {
		        zend_ast_process(CG(ast));
		    }

		    zend_file_context_begin(&original_file_context); //初始化CG(file_context)
		    zend_oparray_context_begin(&original_oparray_context); //初始化CG(context)
		    zend_compile_top_stmt(CG(ast)); //AST->zend_op_array编译流程
		    zend_emit_final_return(&retval_zv); //设置最后的返回值
		    op_array->line_start = 1;
		    op_array->line_end = CG(zend_lineno);
		    pass_two(op_array);
		    zend_oparray_context_end(&original_oparray_context);
		    zend_file_context_end(&original_file_context);

		    CG(active_op_array) = original_active_op_array;
		}
		...
	    }
	    ...

	    return op_array;
	}


compile_file()操作中有几个保存原来值的操作，这是因为这个函数在PHP脚本执行中并不会只执行一次，
主脚本执行时会第一次调用，而include、require也会调用，所以需要先保存当前值，然后执行完再还原回去。

大白话就是 include一个文件之后,相当于在原来的输入后追加一下include文件的内容.(实质为先分词.然后追加到AST上.并不是简单的 $str .= $include_file_contens)

	if (open_file_for_scanning(file_handle)==FAILURE) {//文件打开失败
		...
	} else {
		zend_bool original_in_compilation = CG(in_compilation);
		CG(in_compilation) = 1;
		...
	}

特别注意这里的逻辑.


 PHP的函数上线文是由一个C语言的结构变量实现的.
 C的函数上下文是由汇编实现的.  
 汇编没有上下文，直接对应CPU的指令集.


zend_execute_data是执行过程中最核心的一个结构，每次函数的调用、include/require、eval等都会生成一个新的结构，它表示当前的作用域、代码的执行位置以及局部变量的分配等等，等同于机器码执行过程中stack的角色

 - EG ： 执行宏 结构  zend_executor_globals executor_globals是PHP整个生命周期中最主要的一个结构，是一个全局变量，在main执行前分配(非ZTS下)，直到PHP退出，它记录着当前请求全部的信息
 - CG  : 编译宏 结构
 - SG :  SAPI宏 结构


将IS_CONST、IS_VAR、IS_TMP_VAR类型的操作数、返回值转化为内存偏移量

总结：

到这里整个PHP编译阶段就算全部完成了，最终编译的结果就是zend_op_array，其中最核心的操作就是AST的编译了，有兴趣的可以多写几个例子去看下不同节点类型的处理方式。

另外，编译阶段很关键的一个操作就是确定了各个 变量、中间值、临时值、返回值、字面量 的 内存编号 ，这个地方非常重要，后面介绍执行流程时也会用到。



执行流程
Zend执行opcode的简略过程：

step1: 为当前作用域分配一块内存，充当运行栈，zend_execute_data结构、所有局部变量、中间变量等等都在此内存上分配
step2: 初始化全局变量符号表，然后将全局执行位置指针EG(current_execute_data)指向step1新分配的zend_execute_data，然后将zend_execute_data.opline指向op_array的起始位置
step3: 从EX(opline)开始调用各opcode的C处理handler(即_zend_op.handler)，每执行完一条opcode将EX(opline)++继续执行下一条，直到执行完全部opcode，函数/类成员方法调用、if的执行过程：
step3.1: if语句将根据条件的成立与否决定EX(opline) + offset所加的偏移量，实现跳转
step3.2: 如果是函数调用，则首先从EG(function_table)中根据function_name取出此function对应的编译完成的zend_op_array，然后像step1一样新分配一个zend_execute_data结构，将EG(current_execute_data)赋值给新结构的prev_execute_data，再将EG(current_execute_data)指向新的zend_execute_data，最后从新的zend_execute_data.opline开始执行，切换到函数内部，函数执行完以后将EG(current_execute_data)重新指向EX(prev_execute_data)，释放分配的运行栈，销毁局部变量，继续从原来函数调用的位置执行
step3.3: 类方法的调用与函数基本相同，后面分析对象实现的时候再详细分析
step4: 全部opcode执行完成后将step1分配的内存释放，这个过程会将所有的局部变量"销毁"，执行阶段结束


>>https://github.com/jianhuisu/php7-internal/blob/master/3/zend_executor.md

CV、 常量缩写
VAR  变量缩写

译过程中如果发现当前操作适用缓存机制，则根据缓存数据的大小从cache_size开始分配8或16字节给那个操作数，cache_size向后移动对应大小，然后将起始位置保存于CONST操作数的zval.u2.cache_slot中，执行时直接根据这个值确定缓存位置。
这样可以节省多条指令执行的开销.



master与worker 就此分道扬镳 大陆朝天 各走一边.

（这里面一共有十招 如果你能看懂汉字 能学会5招 如果你同时还懂点C语言 那能学会8招 ，学会5招也比一招不会的强 五十步可以笑百步。当老虎追你们的时候 只要你不是最后一个就行）


在fpm_init()阶段master曾创建了一个全双工的管道：sp，然后在这里创建了一个sp[0]可读的事件，当sp[0]可读时将交由fpm_got_signal()处理，向sp[1]写数据时sp[0]才会可读，那么什么时机会向sp[1]写数据呢？前面已经提到了：当master收到注册的那几种信号时会写入sp[1]端，这个时候将触发sp[0]可读事件。


master: 这个事件是用于限制worker处理单个请求最大耗时的

除了上面这几个事件外还有一个没有提到，那就是ondemand模式下master监听的新请求到达的事件，因为ondemand模式下fpm启动时是不会预创建worker的，有请求时才会生成子进程，所以请求到达时需要通知master进程，这个事件是在fpm_children_create_initial()时注册的，事件处理函数为fpm_pctl_on_socket_accept()，

## php 请求处理

fpm_run()执行后将fork出worker进程，worker进程返回main()中继续向下执行，后面的流程就是worker进程不断accept请求，然后执行PHP脚本并返回。整体流程如下：

(1)等待请求： worker进程阻塞在fcgi_accept_request()等待请求；
(2)解析请求： fastcgi请求到达后被worker接收，然后开始接收并解析请求数据，直到request数据完全到达；(到达是一个过程,并不是一次就能完成)
(3)请求初始化： 执行php_request_startup()，此阶段会调用每个扩展的：PHP_RINIT_FUNCTION()；
(4)编译、执行： 由php_execute_script()完成PHP脚本的编译、执行；
(5)关闭请求： 请求完成后执行php_request_shutdown()，此阶段会调用每个扩展的：PHP_RSHUTDOWN_FUNCTION()，然后进入步骤(1)等待下一个请求。
