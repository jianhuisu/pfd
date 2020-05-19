## php代码的编译与执行

opcodes 结构
zend 虚拟机的知识


c编译生成的是机器码,目标语言直接在物理机上执行
php在的目标语言在虚拟机上执行

php编译生成的是opcodes ，在虚拟机上执行,不能直接在物理机上执行


opcodes 物理机不识别, opcodes由虚拟机 识别执行


1 reg 分析 php代码(超长的字符串)

NFA 不确定有穷状态机 根据模式串 确定一个流程,然后对原串进行逐个判断
DFA 有穷状态机  唯一的上移状态 


使用re2c做词法分析
使用bison 做语法分析

生成 AST -> ast 第归遍历 编译生成 opcodes

词法 语法分析 发生在php_execute_script 阶段

php_execute_script -> zend_execute_scripts -> compile_file -> open_file_for_scanning


zend 
	
	解释层
	中间层   opline 符号表 执行栈
	执行层
	
