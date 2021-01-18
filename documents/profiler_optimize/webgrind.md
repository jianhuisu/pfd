
;最大循环或调试次数,防止死循环
xdebug.max_nesting_level=50

;启用性能检测分析
xdebug.profiler_enable=On

;启用代码自动跟踪
xdebug.auto_trace=on

;允许收集传递给函数的参数变量
xdebug.collect_params=On

;允许收集函数调用的返回值
xdebug.collect_return=On

;指定堆栈跟踪文件的存放目录
xdebug.trace_output_dir="C:\App\php\debug"

;指定性能分析文件的存放目录
xdebug.profiler_output_dir="C:\App\php\debug"

;追加
xdebug.profiler_append=1

;指定追踪文件名格式
;xdebug.profiler_output_name = "cachegrind.out.%c"
xdebug.profiler_output_name = "cachegrind.out.%s"

;远程调试是否开启
xdebug.remote_enable = On

;端口
xdebug.remote_port=9000

;远程调试地址
xdebug.remote_host = 127.0.0.1

;数组或对象最大层数 最大可设置1023
xdebug.var_display_max_depth = 10

;将require,include相关载入的文件名写入追踪文件
xdebug.collect_includes=1

;堆栈追踪
xdebug.default_enable=1

;打印请求方式
xdebug.dump.SERVER=REQUEST_METHOD

;打印GET请求参数
xdebug.dump.GET=*

;打印POST请求参数
xdebug.dump.POST=*

;打印COOKIE
;xdebug.dump.COOKIE=*

;打印UA
;xdebug.dump.SERVER=HTTP_USER_AGENT

xdebug.profiler_output_dir 很明显是用于存放生成的文件的路径
xdebug.profiler_enable profiler功能的开关，默认值0，如果设为1，则每次请求都会生成一个性能报告文件。
xdebug.profiler_enable_trigger 默认值也是0，如果设为1 则当我们的请求中包含XDEBUG_PROFILE参数时才会生成性能报告文件。例如http://localhost/index.php?XDEBUG_PROFILE=1(当然我们必须关闭xdebug.profiler_enable)。使用该功能就捕获不到页面发送的ajax请求，如果需要捕获的话我们就可以使用xdebug.profiler_enable功能。

xdebug.profiler_output_name 生成的文件的名字，默认 cachegrind.out.%t.%p
https://www.cnblogs.com/alex-dong/p/9126904.html

xdebug的性能测试输出文件名是可以配置的。
默认是 xdebug.profiler_output_name = cachegrind.out.%p
那个%p是服务器的pid，会输出“cachegrind.out.1408”之类的文件。
可能这样不太方便测试很多文件的网站。另外对于单一入口的文件名都是一样的.
网上看到的中文文章里面都没有关于这个参数的说明。
我从xdebug官网上找来了它的说明翻译成中文了。

符号含义配置样例样例文件名
%c当前工作目录的crc32校验值trace.%ctrace.1258863198.xt
%p当前服务器进程的pidtrace.%ptrace.5174.xt
%r随机数trace.%rtrace.072db0.xt
%s脚本文件名(注)cachegrind.out.%scachegrind.out._home_httpd_html_test_xdebug_test_php
%tUnix时间戳(秒)trace.%ttrace.1179434742.xt
%uUnix时间戳(微秒)trace.%utrace.1179434749_642382.xt
%H$_SERVER['HTTP_HOST']trace.%Htrace.kossu.xt
%R$_SERVER['REQUEST_URI']trace.%Rtrace._test_xdebug_test_php_var=1_var2=2.xt
%Ssession_id (来自$_COOKIE 如果设置了的话)trace.%Strace.c70c1ec2375af58f74b390bbdd2a679d.xt
%%%字符trace.%%trace.%.xt
注 此项不适用于trace file的文件名