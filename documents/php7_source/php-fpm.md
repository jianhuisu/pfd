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
还有一点需要注意,`woker`抢占式的进行服务.

fpm_run()中`master`进程将进入fpm_event_loop()分支执行

 1. 信号处理,`SIGCHLD`..
 1. fpm_pctl_perform_idle_server_maintenance_heartbeat : worker 数量控制
 1. fpm_pctl_heartbeat : 限制 worker 处理单个请求最大耗时,默认为0 不限制。等价于`php-fpm.conf`中`request_terminate_timeout`配置项.

master进程与worker进程通信是通过共享内存的方式`mmap`.master监控`fpm_scoreboard_proc_s`的值来进行决策.

#### php-fpm 的惊群问题

php-fpm中一个pool监听一个端口,一个pool中可能会有多个进程.~~这意味着会存在多个worker监听同一个port~~.
Linux内核的`SO_REUSEPORT`选项可以实现多个进程监听同一个端口.

一个pool中多个worker进程同时阻塞在`accept`等待监听套接字已建立连接的信息，当内核在该监听套接字上建立一个连接，将同时唤起这些处于accept阻塞的worker进程，
但仅有一个worker进程accept成功,其它worker进程被唤起后没抢到“连接”而再次进入休眠,唤起多余的进程将影响服务器的性能.从而导致`惊群现象`的产生.

#### worker nums 

关于PHP-FPM子进程数量说法正确的有？
A、PHP-FPM 子进程数量不能太多，太多了增加进程管理的开销以及上下文切换的开销
B、dynamic 方式下，最合适的子进程数量为 在 N + 20% 和 M / m 之间 （N 是 CPU 内核数量，M 是 PHP 能利用的内存数量，m 是每个 PHP 进程平均使用的内存数量）
C、static方式：M / (m * 1.2) （M 是 PHP 能利用的内存数量，m 是每个 PHP 进程平均使用的内存数量）
D、pm.max_requests 可以随便设置 ,但是为了预防内存泄漏的风险，还是设置一个合理的数比较好

##### php-fpm 的三种运行模式	

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

#### 实现原理

概括来说，`php-fpm`的实现就是创建一个`master`进程，在`master`进程中创建并监听`socket`，然后`fork`出多个子进程，这些子进程各自`accept`请求，
子进程的处理非常简单，它在启动后阻塞在`accept`上，有请求到达后开始读取请求数据，读取完成后开始处理然后再返回，在这期间是不会接收其它请求的，
也就是说`fpm`的子进程同时只能响应一个请求，只有把这个请求处理完成后才会`accept`下一个请求.

    `man 2 accept`
    int accept(int sockfd, struct sockaddr *addr, socklen_t *addrlen);
    
    It extracts the first connection request on the queue of pending connections for the listening socket
    提取监听端口对应的待处理连接队列中的第一个请求.
    
    params:
    The argument sockfd is a socket that has been created with socket(2), bound to a local address with bind(2), and is listening for connections after a
    listen(2).
    
    The argument addr is a pointer to a sockaddr structure.  This structure is filled in with the address of the peer socket. ....
    
    The addrlen argument is a value-result argument: the caller must initialize it to contain the size (in bytes) of the structure pointed to by addr;

    The  returned  address  is  truncated if the buffer provided is too small; in this case, addrlen will return a value greater than was supplied to the
    call.
    
    If no pending connections are present on the queue, and the socket is not marked as nonblocking, 
    accept() blocks the caller  until  a  connection  is present.  
    ...

首先`accept`是一个系统调用.而且它是阻塞的.

那么`accept`是`accept`什么呢,像master进程一样监听端口吗?答案是否定.`accept`应该是阻塞在自己的`输入缓冲`上,即当一个新的链接建立,`accept`没有什么反映,但是当
有数据从`tcp/ip`的缓冲区copy到`accept`缓冲区时,`accept`就开始有动作了.

`php worker`的同步阻塞机制与`nginx`的事件驱动有很大的区别，`nginx`的子进程通过`epoll`管理套接字，如果一个请求数据还未发送完成则会处理下一个请求.即一个进程会同时连接多个请求，它是非阻塞的模型，只处理活跃的套接字。

#### FAQ 

Q1 : 

nginx的master-worker多进程模型，请求是会被当作一个事件放入到队列中，worker进程会消耗这个队列处理请求，这样并发量就会更高，不会一个worker进程只处理一个请求。
php-fpm也是采用的master-worker，从课程中看出，php-fpm似乎是一个进程同时只能处理一个请求。不知道理解上面存在错误吗？

ANSWER:

同学您好,你提到的nginx的worker同时处理多个请求，是指的I/O多路复用吧？ 比如在支持`epoll`的操作系统下，`nginx`用了epoll来同时处理多个请求。是指的这个意思吧？
那么对于`php-fpm`同样也用了I/O多路复用，一个`worker`的`fpm`也可以同时处理多个请求，在支持`poll`的操作系统下使用的是`poll`，可以`gdb -p worker_pid`，在poll处打一个断点，如下：
//img.mukewang.com/szimg/5c7b50140001d8e125040274.jpg
同样也是可以同时处理多个请求的。 **课程里面提到的意思是多个worker会通过抢锁获取一个请求进行处理**。