## PHP SPAI MODE

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
 - `kill -9 php-fpm-master.pid` , only master process was killed , worker still alive and continue listening port,
    we still can send request through browser and get response normally.
 - `kill -INT/-QUIT/-TERM  master pid` ,  `stop php-fpm service` **信号被当前进程树接收到**.也就是说，不仅当前进程会收到信号，它的子进程也会收到.
 - `kill master pid` 发送SIGTERM 信号到进程 信号可能会被阻塞	
	 
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
   
##### php-fpm 的三种运行模式	

	循环
	pm=dynamic
	pm=static  静态，始终保持一个固定数量的子进程，这个数由（pm.max_children）定义
	pm = ondemand

php-fpm三种对子进程的管理方式
pm = static

静态，始终保持一个固定数量的子进程，这个数由（pm.max_children）定义，这种方式很不灵活，也通常不是默认的。

pm = dynamic

动态，在更老一些的版本中，dynamic被称作apache-like。子进程的数量在下面配置的基础上动态设置：pm.max_children，pm.start_servers，pm.min_spare_servers，pm.max_spare_servers。

启动时，会产生固定数量的子进程（由pm.start_servers控制）可以理解成最小子进程数，而最大子进程数则由pm.max_children去控制，OK，这样的话，子进程数会在最大和最小数范围中变化，还没有完，闲置的子进程数还可以由另2个配置控制，分别是pm.min_spare_servers和pm.max_spare_servers，也就是闲置的子进程也可以有最小和最大的数目，而如果闲置的子进程超出了pm.max_spare_servers，则会被杀掉。

可以看到，pm = dynamic模式非常灵活，也通常是默认的选项。但是，dynamic模式为了最大化地优化服务器响应，会造成更多内存使用，因为这种模式只会杀掉超出最大闲置进程数（pm.max_spare_servers）的闲置进程，比如最大闲置进程数是30，最大进程数是50，然后网站经历了一次访问高峰，此时50个进程全部忙碌，0个闲置进程数，接着过了高峰期，可能没有一个请求，于是会有50个闲置进程，但是此时php-fpm只会杀掉20个子进程，始终剩下30个进程继续作为闲置进程来等待请求，这可能就是为什么过了高峰期后即便请求数大量减少服务器内存使用却也没有大量减少，也可能是为什么有些时候重启下服务器情况就会好很多，因为重启后，php-fpm的子进程数会变成最小闲置进程数，而不是之前的最大闲置进程数。

pm = ondemand

进程在有需求时才产生，与 dynamic 相反，pm.start_servers 在服务启动时即启动。

这种模式把内存放在第一位，他的工作模式很简单，每个闲置进程，在持续闲置了pm.process_idle_timeout秒后就会被杀掉，有了这个模式，到了服务器低峰期内存自然会降下来，如果服务器长时间没有请求，就只会有一个php-fpm主进程，当然弊端是，遇到高峰期或者如果pm.process_idle_timeout的值太短的话，无法避免服务器频繁创建进程的问题，因此pm = dynamic和pm = ondemand谁更适合视实际情况而定。
