zend_types.h


typedef struct _zval_struct     zval;

struct _zval_struct {
	zend_value value;   // store value 
	union u1;           // variable tag 
	union u2;
};

详细一点的：

struct _zval_struct {
	zend_value        value;		
	union {
		struct {
			ZEND_ENDIAN_LOHI_4(
				zend_uchar    type,	   // var real type 	
				zend_uchar    type_flags,  // var is const ? reference ? callable ? 
				zend_uchar    const_flags, // const 
				zend_uchar    reserved)	   // 保留字段 
		} v;
		uint32_t type_info;
	} u1;
	union {
		uint32_t     next;                 /* hash collision chain */
		uint32_t     cache_slot;           /* literal cache slot */
		uint32_t     lineno;               /* line number (for ast nodes) */
		uint32_t     num_args;             /* arguments number for EX(This) */
		uint32_t     fe_pos;               /* foreach position */
		uint32_t     fe_iter_idx;          /* foreach iterator index */
		uint32_t     access_flags;         /* class constant access flags */
		uint32_t     property_guard;       /* single property guard */
	} u2;
};

zval一共16个字节 可以表示 任意的一个php变量 

typedef union _zend_value {
	zend_long         lval;				/* long value */
	double            dval;				/* double value */
	zend_refcounted  *counted;
	zend_string      *str;
	zend_array       *arr;
	zend_object      *obj;
	zend_resource    *res;
	zend_reference   *ref;
	zend_ast_ref     *ast;
	zval             *zv;
	void             *ptr;
	zend_class_entry *ce;
	zend_function    *func;
	struct {
		uint32_t w1;
		uint32_t w2;
	} ww;
} zend_value;

除了 整型与浮点存储值本身（不要间接寻址 ） ，其他变量都是存储的 指针
变量类型隐式表明了数据本身占用的长度


针对于联合体 u1 而言,成员 u1.type 取值

	/* regular data types */
	#define IS_UNDEF					0
	#define IS_NULL						1
	#define IS_FALSE					2
	#define IS_TRUE						3
	#define IS_LONG						4
	#define IS_DOUBLE					5
	#define IS_STRING					6
	#define IS_ARRAY					7
	#define IS_OBJECT					8
	#define IS_RESOURCE					9
	#define IS_REFERENCE				10

	/* constant expressions */
	#define IS_CONSTANT					11
	#define IS_CONSTANT_AST				12

	/* fake types */
	#define _IS_BOOL					13
	#define IS_CALLABLE					14
	#define IS_ITERABLE					19
	#define IS_VOID						18

	/* internal types */
	#define IS_INDIRECT             	15
	#define IS_PTR						17
	#define _IS_ERROR					20

确定一个变量的值的流程为

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



二进制安全 于 非 二进制安全

    struct {
        int 	len 
        char	val
    }


COW 除int /doubule 直接复制 外,string 都是写时复制



#### 空间问题

最近看一下php7源码

其中有个`string`的`struct`

    struct _zend_string {
            zend_refcounted_h gc;
            zend_ulong        h;                /* hash value */
            size_t            len;
            char              val[1];
    };
    
一直想不通为什么char数组而且是一个呢，为什么不是`char*`
终于想明白啦，因为最后用的val,它只是一个指向而已，而且`char[1]`的占位1个字节，
如果用`char*` 就是一个指针字节（32系统就是4字节，64位系统就是8字节）空间问题呀



