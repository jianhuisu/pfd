## PHP SPAI MODE

在PHP生命周期的各个阶段，一些与服务相关的操作都是通过SAPI接口实现。 
各个服务器抽象层之间遵守着相同的约定，这里我们称之为SAPI接口。
在PHP的源码中，当需要调用服务器相关信息时，全部通过SAPI接口中对应的方法调用实现

    php-fpm + nginx
    php + terminal
    ... 
    
#### PHP常见的四种运行模式

`SAPI（Server Application Programming Interface）`服务器应用程序编程接口，即PHP与其他应用交互的接口.
每个`SAPI`实现都是一个`_sapi_module_struct`结构体变量。
PHP脚本要执行有很多方式，通过Web服务器，或者直接在命令行下，也可以嵌入在其他程序中。
`SAPI`提供了一个和外部通信的接口，常见的`SAPI`有：`cgi`、`fast-cgi`、`cli`、`isapi` apache模块的DLL
 
 1. `ISAPI`模式 (eg Apache : apache2handler mode ) 以web服务器的一个模块加载运行,其实就是将PHP的源码与webServer的代码一起编译，运行时是同一个进程,共享同一个地址空间. 例如 LAMP中,PHP就是作为Apache的一个模块运行的.Apache是多线程调用php模块的.(same as IIS)
 1. `CGI`模式  `fork-and-execute` webServer将动态请求转发到CGI程序(以php为例子),就相当于fork一个子进程,然后`exec(php process)`,用CGI程序来解释请求内容,最后将子进程的`output`返回.此时webServer与php进程的地址空间是独立的.此时的php是作为一个独立的程序运行.
 1. `FastCGI`模式 这种形式是CGI的加强版本，CGI是单进程，多线程的运行方式，程序执行完成之后就会销毁，所以每次都需要加载配置和环境变量（创建-执行）。
   而FastCGI则不同，FastCGI 是一个常驻 (long-live) 型的 CGI，它可以一直执行着，只要激活后，不会每次都要花费时间去 fork 一次。
 1. `CLI` command line interface

#### CLI 

	php_module_startup
	php_request_startup
	php_execute_script
	php_request_shutdown
	php_module_shutdown


#### PHP-FPM

`php 5.3.3` 以后的`php-fpm`不再支持`php-fpm (start|stop|reload)`等命令，需要使用信号控制.`php-fpm master`进程可以理解以下信号
    
 - `kill -USR1 "php-fpm master pid"` 重新打开日志文件. 执行完毕后 你会发现`php-fpm master/worker`进程`id` **not change**  
 - `kill -USR2 "php-fpm master pid"`  平滑重载所有`php-fpm`进程,执行完毕后你会发现`php-fpm master/worker`进程`id` **have changed**.
 - `kill -KILL/-9 php-fpm-master.pid` , 强制杀死master进程,该信号不允许中断/阻塞,此时master进程无法通知回收worker进程,所以此时`worker`进程仍然监听port,仍然可以正常处理http请求.
 - `kill -INT/-QUIT/-TERM  master pid` ,  `stop php-fpm service` **信号被当前进程树接收到**.也就是说，不仅当前进程会收到信号，它的子进程也会收到.
 - `kill master pid` 发送`SIGTERM`信号到进程 信号可能会被阻塞,`master`可以回收worker进程.	
	 
example.

    [sujianhui@dev529 ~]$>ps aux | grep php-fpm
    root     17000  0.0  0.0 243220  7208 ?        Ss   17:00   0:00 php-fpm: master process (/usr/local/php/etc/php-fpm.conf)
    sujianh+ 17001  0.0  0.0 245304  7072 ?        S    17:00   0:00 php-fpm: pool www
    sujianh+ 17002  0.0  0.0 245304  7072 ?        S    17:00   0:00 php-fpm: pool www
    sujianh+ 17069  0.0  0.0 112816   976 pts/3    S+   17:01   0:00 grep --color=auto php-fpm
    
    [sujianhui@dev529 ~]$>sudo kill -USR1 17000
    [sujianhui@dev529 ~]$>ps aux | grep php-fpm
    root     17000  0.0  0.0 243220  7208 ?        Ss   17:00   0:00 php-fpm: master process (/usr/local/php/etc/php-fpm.conf)
    sujianh+ 17001  0.0  0.0 245304  7072 ?        S    17:00   0:00 php-fpm: pool www
    sujianh+ 17002  0.0  0.0 245304  7072 ?        S    17:00   0:00 php-fpm: pool www
    sujianh+ 17105  0.0  0.0 112816   972 pts/3    S+   17:01   0:00 grep --color=auto php-fpm
    
    
    [sujianhui@dev529 ~]$>sudo kill -USR2 17000
    [sujianhui@dev529 ~]$>ps aux | grep php-fpm
    root     17122  0.0  0.0 243220  7212 ?        Ss   17:01   0:00 php-fpm: master process (/usr/local/php/etc/php-fpm.conf)
    sujianh+ 17123  0.0  0.0 245304  7072 ?        S    17:01   0:00 php-fpm: pool www
    sujianh+ 17124  0.0  0.0 245304  7072 ?        S    17:01   0:00 php-fpm: pool www
    sujianh+ 17126  0.0  0.0 112816   976 pts/3    S+   17:01   0:00 grep --color=auto php-fpm
    
    [sujianhui@dev529 ~]$>pstree 17122 -a
    php-fpm
      ├─php-fpm          
      └─php-fpm          
    [sujianhui@dev529 ~]$>sudo kill -INT 17122
    [sujianhui@dev529 ~]$>ps aux | grep php-fpm
    sujianh+ 17229  0.0  0.0 112816   976 pts/3    S+   17:03   0:00 grep --color=auto php-fpm

nginx的master-worker机制与fpm大体相同.但是有一个问题需要注意,使用systemctl启动起来的master被kill以后，worker也会死掉.

正常启动nginx,kill掉master

    [sujianhui@dev0529 sbin]$>which nginx
    /usr/sbin/nginx
    [sujianhui@dev0529 sbin]$>sudo nginx 
    [sujianhui@dev0529 sbin]$>ps aux | grep nginx
    root      4562  0.0  0.0  46608  1084 ?        Ss   21:46   0:00 nginx: master process nginx
    sujianh+  4563  0.0  0.0  49128  2088 ?        S    21:46   0:00 nginx: worker process
    sujianh+  4578  0.0  0.0 112812   972 pts/0    S+   21:46   0:00 grep --color=auto nginx
    
    [sujianhui@dev0529 sbin]$>sudo kill -9 4562
    [sujianhui@dev0529 sbin]$>ps aux | grep nginx
    sujianh+  4563  0.0  0.0  49128  2088 ?        S    21:46   0:00 nginx: worker process
    sujianh+  4612  0.0  0.0 112812   972 pts/0    S+   21:46   0:00 grep --color=auto nginx
    [sujianhui@dev0529 sbin]$>kill -9 4563
    [sujianhui@dev0529 sbin]$>ps aux | grep nginx
    sujianh+  4638  0.0  0.0 112812   972 pts/0    S+   21:47   0:00 grep --color=auto nginx
    
使用systemctl启动的master被kill掉以后,worker也会杀掉

    [sujianhui@dev0529 sbin]$>systemctl start nginx
    [sujianhui@dev0529 sbin]$>ps aux | grep nginx
    root      4678  0.0  0.0  46608  1072 ?        Ss   21:47   0:00 nginx: master process /usr/sbin/nginx -c /etc/nginx/nginx.conf
    sujianh+  4679  0.0  0.0  49124  2080 ?        S    21:47   0:00 nginx: worker process
    sujianh+  4702  0.0  0.0 112812   972 pts/0    S+   21:47   0:00 grep --color=auto nginx
    [sujianhui@dev0529 sbin]$>sudo kill -9 4678
    [sujianhui@dev0529 sbin]$>ps aux | grep nginx
    sujianh+  4732  0.0  0.0 112812   972 pts/0    S+   21:47   0:00 grep --color=auto nginx


   
#### apache prefork