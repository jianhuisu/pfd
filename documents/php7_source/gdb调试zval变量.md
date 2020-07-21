## gdb调试zval变量

注意1.php

	<?php

        $a = 2;
        echo $a;

        $b = 1.1;
        echo $b;

        $c = null;
        echo $c;

        $d = true;
        echo $d;

        $e="string";
        echo $e;

        $g = [1,2,3];
        echo $g;
	

1 `gdb p z` 查看zval结构变量的值,如果是一个zval 指针,那么使用`gdb > p *z`
2 查看 `u1.type `的值,然后根据预定义的映射关系,去`zend_value`中 进行适配 查找真实值


	(gdb) c
	Continuing.
	1.1
	Breakpoint 1, ZEND_ECHO_SPEC_CV_HANDLER () at /home/sujianhui/CLionProjects/php-7.1.0/Zend/zend_vm_execute.h:34640
	34640           SAVE_OPLINE();
	(gdb) n
	34641           z = _get_zval_ptr_cv_undef(execute_data, opline->op1.var);
	(gdb) p z
	$7 = (zval *) 0x7ffff5e150a0
	(gdb) p *z

3 间接寻址时 比如字符串如何查看值

	(gdb) p $12.value.str
	$13 = (zend_string *) 0x10e9de0
	(gdb) p *$12.value.str
	$14 = {
	  gc = {
	    refcount = 1, 
	    u = {
	      v = {
		type = 6 '\006', 
		flags = 7 '\a', 
		gc_info = 0
	      }, 
	      type_info = 1798
	    }
	  }, 
	  h = 9223378990886268924, 
	  len = 6, 
	  val = "s"
	}
	(gdb) p *$12.value.str.val@6  // 从指针地址开始 长度为6 取6个字节
	$15 = "string"


！！！！注意元素地址的内存地址
所有变量都是连续地址存放的？
COW 除int /doubule 直接复制 外,string 都是写时复制



#### 空间问题

其中有个`string`的`struct`

    struct _zend_string {
            zend_refcounted_h gc;
            zend_ulong        h;                /* hash value */
            size_t            len;
            char              val[1];
    };
    
一直想不通为什么`char`数组而且是一个呢，为什么不是`char*`.终于想明白啦，因为最后用的`val`,它只是一个指向而已，而且`char[1]`的占位1个字节，
如果用`char*` 就是一个指针字节（`32`系统就是`4`字节，`64`位系统就是`8`字节）空间问题呀.然后可以根据`len`找出所有剩余的所有字符.



