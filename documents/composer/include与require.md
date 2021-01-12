# include 与 require

`include`或`require`语句会获取指定文件中存在的所有文本/代码/标记，并复制到使用`include`语句的文件中。
也就是说 `array_merge('a.php','b.php')` 两个文件中的`token`.

合并操作发生在服务器执行之前 ？

include 和 require 语句是相同的，除了错误处理方面：

 - require 会生成致命错误（E_COMPILE_ERROR）并停止脚本
 - include 只生成警告（E_WARNING），并且脚本会继续

如非必要,请始终使用`require`向执行流引用关键文件。这有助于提高应用程序的安全性和完整性.

另外与include/require就不得不提另外一个问题.`使用include还是include_once`

 - include_once需要查询一遍已加载的文件列表, 确认是否存在, 然后再加载. 
 - open_path 

`鸟哥`一直认为, 我们应该使用include, 而不是include_once, 因为我们完全能做到自己规划, 一个文件只被加载一次. 还可以借助自动加载, 来做到这一点. 你使用include_once, 只能证明, 你对自己的代码没信心.

## 参考资料

https://www.laruence.com/2012/09/12/2765.html



