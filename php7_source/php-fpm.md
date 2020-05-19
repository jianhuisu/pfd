## php-fpm

### nginx 与 php-fpm 通讯流程

    ----> browser client send request 
    ----> DNS domain to ip
    ----> send HTTP/https to server ip
    ----> nginx 解http包 组fastcgi包
    ----> php-fpm:worker process 解fastcgi包
    ----> php-fpm:worker组fastcgi包
    ----> nginx 解fastcgi包 组http包
    ----> browser client

启动php-fpm 

	sudo /usr/local/php/sbin/php-fpm -c /etc/php-fpm.d/www.conf

if php-fpm is not running as root,  worker child owner is current user : $USER 

fpm_main.c

    //注册SAPI:将全局变量sapi_module设置为cgi_sapi_module
    sapi_startup(&cgi_sapi_module); 
    
    fpm_init()
    
        fpm_conf_init_main                 load php-fpm.d/pool.conf
        fpm_scoreboard_init_main           分配用于记录 worker 进程运行信息的共享内存  ,用于 master 与 worker 进程通信
        fpm_signals_init_main              设置 master 的信号处理 handler，当 master 收到 SIGTERM、SIGINT、SIGUSR1、这些信号时将调用sig_handler()处理
        fpm_sockets_init_main()            创建每个 worker pool 的 socket 套接字  
        fpm_event_init_main()              
        
    fpm_run():fork
    
        -> parent : master -----> monitor child  
        -> child  : worker -----> listen ,accept 
        

##### worker 

`worker`进程池维护一定数量的进程不销毁，等待连接进来后直接execute，少去了fork进程以及销毁进程的系统开销

(1)等待请求： worker进程阻塞在fcgi_accept_request()等待fastcgi请求；  

    FCGI_LOCK(req->listen_socket);
    req->fd = accept(listen_socket, (struct sockaddr *)&sa, &len);
    FCGI_UNLOCK(req->listen_socket);
    					    					
(2)解析请求： fastcgi请求到达后被worker接收，然后开始接收并解析请求数据，直到request数据完全到达；
(3)请求初始化： 执行php_request_startup()，此阶段会调用每个扩展的：PHP_RINIT_FUNCTION()；
(4)编译、执行： 由php_execute_script()完成PHP脚本的编译、执行；
(5)关闭请求： 请求完成后执行php_request_shutdown()，此阶段会调用每个扩展的：PHP_RSHUTDOWN_FUNCTION()，然后进入步骤(1)等待下一个请求。

    while(fcgi_accept_request()){
        php_request_startup()
            PHP_RINIT_FUNCTION()
            php_execute_script()
            PHP_RSHUTDOWN_FUNCTION()
        php_request_shutdown()
    }

1. MINIT：Php扩展的初始化方法，整个模块启动时候被调用一次
2. RINIT：Php扩展的初始化方法，每个请求会调用一次
    
worker 进程一次请求的处理被划分为 5 个阶段：

 - FPM_REQUEST_ACCEPTING:        等待请求阶段
 - FPM_REQUEST_READING_HEADERS:  读取 fastcgi 请求 header 阶段
 - FPM_REQUEST_INFO:             获取请求信息阶段，此阶段是将请求的 method、query stirng、request uri 等信息保存到各 worker 进程的 fpm_scoreboard_proc_s 结构中，此操作需要加锁，因为 master 进程也会操作此结构
 - FPM_REQUEST_EXECUTING:        执行请求阶段
 - FPM_REQUEST_END:              没有使用
 - FPM_REQUEST_FINISHED:         请求处理完成
 
worker 处理到各个阶段时将会把当前阶段更新到`fpm_scoreboard_proc_s->request_stage`，master进程正是通过这个标识判断`worker`进程是否空闲的。  
       

##### master : 管理worker进程, 信号处理


PHP-FPM 的进程管理方式和Nginx的进程管理方式有些类似。在处理请求时，并非由主进程接受请求后转给子进程，而是子进程「抢占式」地接受用户请求。
本质上`PHP-FPM`多进程以及`Nginx`多进程，都是在主进程监听同一个端口后，fork子进程达到多个进程监听同一端口的目的。

php-fpm在`pm=static` `pm=dynamic`两种运行模式下,master进程是不会监听是否有新请求到达的，只有worker进程负责监听处理新的请求(这意味着`kill -9 master.pid`后,cgi服务仍然是可用的,worker进程仍然在监听端口).
这是因为在启动阶段,这两种模式下,都会提前fork出worker进程,然后master进程只负责监控worker进程的运行状态即可.但是在`pm=ondemand`模式下,master需要监听是否有新请求到达,
如果有新请求到达,master需要`fork worker`.一般`ondemand`较少使用,所以下面主要介绍`pm=static`,`pm=dynamic`两种模式.


`php-fpm`启动进程A会调用`MINIT`方法，然后`fork`出一个`fpm-master`进程B，进程B启动多个`php-cgi`子进程C，启动工作完成后，启动进程A就退出了.其它进程常驻内存.
master 启动了一个定时器，每隔 1s 触发一次，主要用于 dynamic、ondemand 模式下的 worker 管理，
master 会定时检查各 worker pool 的 worker 进程数，通过此定时器实现 worker 数量的控制.
还有一点需要注意,woker抢占式的进行服务.


fpm_run()中`master`进程将进入fpm_event_loop()分支执行

 1. 信号处理,`SIGCHLD`..
 1. fpm_pctl_perform_idle_server_maintenance_heartbeat : worker 数量控制
 1. fpm_pctl_heartbeat : 限制 worker 处理单个请求最大耗时,默认为0 不限制。等价于`php-fpm.conf`中`request_terminate_timeout`配置项.

master进程与worker进程通信是通过共享内存的方式`mmap`.master监控`fpm_scoreboard_proc_s`的值来进行决策.

#### php-fpm 的惊群问题

php-fpm中一个pool监听一个端口,一个pool中可能会有多个进程.这意味着会存在多个worker监听同一个port.
Linux内核的SO_REUSEPORT选项可以实现多个进程监听同一个端口.

一个pool中多个worker进程同时阻塞在`accept`等待监听套接字已建立连接的信息，当内核在该监听套接字上建立一个连接，将同时唤起这些处于accept阻塞的worker进程，
但仅有一个worker进程accept成功,其它worker进程被唤起后没抢到“连接”而再次进入休眠,唤起多余的进程将影响服务器的性能.从而导致`惊群现象`的产生.


