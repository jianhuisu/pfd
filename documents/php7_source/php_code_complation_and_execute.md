## php代码的编译与执行

PHP简化执行过程： 
    
 1. 扫描(scanning) ,将index.php内容变成一个个语言片段(token) 
 1. 解析(parsing) , 将一个个语言片段变成有意义的表达式 
 1. 编译(complication),将表达式编译成中间码(opcode) 
 1. 执行(execution),将中间码一条一条的执行 
 1. 输出(output buffer),将要输出的内容输出到缓冲区
 
#### php脚本解析执行流程 

php总共包括3个模块：

 - php内核，
 - zend引擎
 - php扩展层。

ZendVM有编译和执行两个模块。

 1. 词法分析，语法分析 使用`re2c`、`bison`完成，旧的PHP版本直接生成了opcode,php7.x的版本在语法分析阶段生成AST，目的是将PHP的编译过程和执行过程解耦
 1. 编译AST, 编译出来的不是汇编语言，而是ZendVM可以识别的中间指令.生成opcodes指令集 `op_array(opline1,opline2, ...)` . `opline: opcode op1 op2`

词法 语法分析发生在`php_execute_script`阶段.`php_execute_script` -> `zend_execute_scripts` -> `compile_file` -> `open_file_for_scanning`

zendVM 3 important points

 1. 指令集             
 1. stack
 1. symbol tables

c编译生成的是机器码,目标语言直接在物理机上执行,php在的目标语言在虚拟机上执行
php编译生成的是opcodes ，在虚拟机上执行,不能直接在物理机上执行
opcodes物理机不识别, opcodes由虚拟机识别执行

这也是解释型语言和静态编译型语言不同的一点，编译出来的不是汇编语言，而是ZendVM可以识别的中间指令

#### code 

opcode位置 ： zend/zend_vm_opcodes.h

	struct _zend_op {
	    const void *handler;
	    znode_op op1;
	    znode_op op2;
	    znode_op result;
	    uint32_t extended_value;
	    uint32_t lineno;
	    zend_uchar opcode;
	    zend_uchar op1_type;
	    zend_uchar op2_type;
	    zend_uchar result_type;
	};
	 
	typedef struct _zend_op zend_op;

`op_array`是包含编译过程中产生的所有单个`opline`的集合，不仅仅包含`opline`的集合数组,同样还含有其他在编译过程动态生成的关键数据.

以上就是操作数部分信息储存的地方。可以看到在`zend_op_array`里面仅分配了CV变量名数组，但是这里面并没有储存CV变量值的地方，
同样TMP_VAR和VAR变量亦是如此，也只有一个简单数量统计。
对应的变量值储存在另外一个结构上，那么他们的具体的值应该在什么样的结构上分配呢？接着又引出了`zend_execute_data`结构。

	struct _zend_execute_data {
	    const zend_op       *opline;           /* executed opline                */
	    zend_execute_data   *call;             /* current call                   */
	    zval                *return_value;
	    zend_function       *func;             /* executed function              */
	    zval                 This;             /* this + call_info + num_args    */
	    zend_execute_data   *prev_execute_data;
	    zend_array          *symbol_table;
	#if ZEND_EX_USE_RUN_TIME_CACHE
	    void               **run_time_cache;   /* cache op_array->run_time_cache */
	#endif
	};

`zend_execute_data`相当于在执行编译`oplines`的`Context(上下文)`，是通过具体的某个`zend_op_array`的结构信息初始化产生的。
所以一个`zend_execute_data`对应一个`zend_op_array`,这个结构用来存储在解释运行过程产生的局部变量，当前执行的`opline`，
上下文之间调用的关系，调用者的信息，符号表等。所以我们想要知道的CV变量，TMP_VAR, VAR变量其实是分配在这个结构上面的，而且还是动态分配紧挨在这个结构后面的。


根据前面的目标，我们对整个指令集其实已经了解的差不多了，现在需要探究每一条指令集的解释过程即对应handler处理函数
ZendVM里面对于handler的处理全部定义在zend_vm_execute.h 中，这个文件其实是自动生成的
handler指向的是处理函数

#### re2c bison 

1 reg 分析 php代码(超长的字符串)

NFA 不确定有穷状态机 根据模式串 确定一个流程,然后对原串进行逐个判断
DFA 有穷状态机  唯一的上移状态 

使用re2c做词法分析
使用bison 做语法分析 ba ke en fan shi
生成 AST -> ast 第归遍历 编译生成 opcodes

#### reference 

https://github.com/pangudashu/php7-internal/blob/master/3/zend_compile_opcode.md
