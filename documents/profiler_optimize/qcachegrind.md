# qcachegrind

## 背景

可量化是问题解决的前提条件.性能优化不能仅仅局限于理论分析,要通过性能分析工具辅助来得出量化指标.从而更加准确的去衡量优化方案.
每种成熟的语言都有自己的性能监测工具.例如:

 - golang : pprof
 - php : xdebug ， xhprof

这些性能监测工具通过钩子埋点等方式对程序的耗时，内存使用量,调用顺序等关键数据进行记录.从而生成响应的监测报告.
这些文件生成时都是按照统一的报告协议进行记录,这样便于使用成熟的分析工具(或自研工具)来解析分析报告.同时也便于
开发通用的性能分析工具来解析不同语言产生的分析报告.

市面上比较流行的单机的性能监测工具有:

 - qcachegrind -> platform:Win / MacOS
 - kcachegrind -> platform:Linux
 - webgrind  -> platform: Linux / Win / MacOS 这个非常棒

kcachegrind 与 qcachegrind实质上是同一个东西,只不过两者使用的图形引擎不同.kcachegrind基于KDE. 而qcachegrind基于Qt.

xdebug 生成性能分析文件,qcachegrind格式化该文件,提供良好的可读性.

    brew install graphviz
    brew install qcachegrind

## MacOS: qcachegrind 安装

 1. `brew install graphviz`  执行此命令安装graphviz，用来Call Graph功能  
 1. `brew install qcachegrind`  安装qcachegrind. (如果 call graph 功能不能使用.请`sudo ln -s /usr/local/bin/dot /usr/bin/dot`)
 
运行`qcachegrind`

    #> nohup qcachegrind &    

## dashboard 指标解读

 - `invocation count`
 - `total inclusive cost` => `incl` shows the time consumed by the function including callees
 - `total self count`  shows the time consumed by the function not including callees cost
 
单词释义

 - caller  调用者 
 - callees 被调用者
 - invocation 调用
 - invoke 调用,援引 

## xdebug 配置实例


    [xdebug]
    
    zend_extension=/usr/lib/php/extensions/no-debug-non-zts-20160303/xdebug.so
    xdebug.remote_enable=on
    xdebug.idekey='PHPSTORM'
    xdebug.remote_host=127.0.0.1
    xdebug.remote_port=9001
    
    ; 性能分析部分
    xdebug.profiler_enable=Off
    ; 开启性能分析触发模式 而不是对于每一个请求都触发性能分析 与 xdebug.profiler_enable=0 时配合使用:w
    xdebug.profiler_enable_trigger=On
    ; url中包含XDEBUG_PROFILE参数对时才会触发性能分析 eg. http://local.web.vhall.com/index.php?XDEBUG_PROFILE=aaa
    xdebug.profiler_enable_trigger_value='aaa'
    xdebug.profiler_output_dir="/Users/sujianhui/PhpstormProjects/profiler_output"
    xdebug.profiler_output_name=callgrind.out.%u

## 参考资料

qcachegrind安装指南 https://blog.csdn.net/weixin_33881753/article/details/88923382
QCacheGrind工具使用简介 https://blog.csdn.net/raoxiaoya/article/details/111994696
xdebug速查表 https://blog.csdn.net/qq624202120/article/details/64124087
phpstorm中xdebug的配置 https://www.cnblogs.com/yjken/p/8435018.html
仪表盘参数解读 https://stackoverflow.com/questions/33094913/how-do-i-read-the-ui-of-qcachegrind