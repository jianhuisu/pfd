# 

首先明确 php 中的 错误 与 异常 是两个概念


异常可以使用 try catch 进行捕获.
而错误需要使用 set_error_handler 捕获.



    <?php
 // 用户定义的错误处理函数
 function myErrorHandler($errno, $errstr, $errfile, $errline) {
     echo "<b>Custom error:</b> [$errno] $errstr<br>";
     echo " Error on line $errline in $errfile<br>";
 }

 // 设置用户定义的错误处理函数
 set_error_handler("myErrorHandler");

 $test=2;

 // 触发错误
 if ($test>1) {
     trigger_error("A custom error has been triggered");
 }
 ?> 


如果不设置exit. 貌似错误会冒泡.

