## swoole

原文文档 https://wiki.swoole.com/#/learn?id=server%e7%9a%84%e4%b8%a4%e7%a7%8d%e8%bf%90%e8%a1%8c%e6%a8%a1%e5%bc%8f%e4%bb%8b%e7%bb%8d

swoole的协程模型与go的协程模型不同 不论协程的调度 还是多核的利用上,要注意区别

 - go 协程抢占式调度
 - swoole 协程非抢占式调用 

Swoole中的网络请求处理是基于事件的，并且充分利用了底层的`epoll / kqueue`实现，使得为数百万个请求提供服务变得非常容易。

查看安装的swoole版本

    [sujianhui@dev0529 ~]$>php -r "echo SWOOLE_VERSION;"
    4.5.2

swoole快速启动中的例子大部分都是异步风格的编程模式，用协程风格(就是用协程同样可以实现相同的功能)。
协程使得原有的异步逻辑同步化，但是在协程的切换是隐式发生的，所以在协程切换的前后不能保证全局变量以及`static`变量的一致性。

    快速启动
        TCP服务器
        UDP服务器
        HTTP服务器
        WebSocket服务器
        MQTT(物联网)服务器
        执行异步任务(Task)
        协程初探
        
原文地址https://wiki.swoole.com/#/start/start_server


##### 端口监听

多协议端口复合使用 ： 监听一个端口,该端口同时支持多种协议

    $port1 = $server->listen("127.0.0.1", 9501, SWOOLE_SOCK_TCP);
    $port1->set([
        'open_websocket_protocol' => true, // 设置使得这个端口支持WebSocket协议
        'open_http_protocol' => false, // 设置这个端口关闭HTTP协议功能
    ]);

一个server同时监听多个端口
    
    // 这是主服务支持协议
    $server = new Swoole\WebSocket\Server("0.0.0.0", 9514, SWOOLE_BASE);
    
    //返回port对象
    $port1 = $server->listen("127.0.0.1", 9501, SWOOLE_SOCK_TCP);
    $port2 = $server->listen("127.0.0.1", 9502, SWOOLE_SOCK_UDP);
    $port3 = $server->listen("127.0.0.1", 9503, SWOOLE_SOCK_TCP | SWOOLE_SSL);

    //port对象的调用set方法
    $port1->set([
        'open_length_check' => true,
        'package_length_type' => 'N',
        'package_length_offset' => 0,
        'package_max_length' => 800000,
    ]);
    
    $port3->set([
        'open_eof_split' => true,
        'package_eof' => "\r\n",
        'ssl_cert_file' => 'ssl.cert',
        'ssl_key_file' => 'ssl.key',
    ]);
    
    //设置每个port的回调函数
    $port1->on('connect', function ($serv, $fd){
        echo "Client:Connect.\n";
    });
    
    $port1->on('receive', function ($serv, $fd, $from_id, $data) {
        $serv->send($fd, 'Swoole: '.$data);
        $serv->close($fd);
    });
    
    $port1->on('close', function ($serv, $fd) {
        echo "Client: Close.\n";
    });
    
    $port2->on('packet', function ($serv, $data, $addr) {
        var_dump($data, $addr);
    });   

##### 什么是协程（纯用户态的线程）

协程可以简单理解为线程，只不过这个线程是纯用户态的，不需要操作系统参与，创建销毁和切换的成本非常低，
和线程不同的是swoole协程没法利用多核`cpu`的，想利用多核`cpu`需要依赖`Swoole`的多进程模型。(go的协程模型可以利用多核CPU)

 - 协程是一种用户态的线程 
 - 一个线程可以有多个协程 
 - 一个进程可以有多个协程  
 - swoole协程主要依赖于 c 的 setjmp longjmp (类似于 goto )的实现

`channel`可以理解为消息队列，只不过是协程间的消息队列，多个协程通过 `push` 和 `pop` 操作生产消息和消费消息，用来协程之间的通讯。
注意: **`channel` 是没法跨进程的，只有同一个`Swoole`进程里的协程才能同通讯**.

##### swoole 协程调度

用户的每个请求都会创建一个协程，请求结束后协程结束，如果同时有成千上万的并发请求，**某一时刻某个进程内部会存在成千上万的协程**.
大家知道多线程是为了提高程序的并发，同样的多协程也是为了提高并发。

swoole的协程是同步的,但是同时它也是非阻塞的.

 1. 协程代码段中发现代码遇到了，`Co::sleep()` 或者产生了`网络IO`,例如`MySQL->query()`,`Swoole`就会把这个`Mysql`连接的`Fd`放到 `EventLoop` 中
 1. 协程主动出让CPU给其它协程使用.`yield`
 1. 等待`MySQL`数据返回后就继续执行这个协程,`resume`

eg. 

    go(function () {
        MySQL->query(); 
    });

协程的创建、切换、挂起、销毁全部为内存操作，消耗是非常低的,所以程序仅启动了一个1个进程，就可以并发处理大量请求。
程序的性能基本上与异步回调方式相同，但是代码完全是同步编写的.

##### 同步IO

什么是同步 IO：

简单的例子就是执行到 MySQL->query 的时候，这个进程什么事情都不做，等待 MySQL 返回结果，返回结果后再向下执行代码，所以同步 IO 的服务并发能力是很差的。

什么样的代码是同步 IO：

没有开启一键协程化的时候，那么你的代码里面绝大部分涉及 IO 的操作都是同步 IO 的，协程化后，就会变成异步 IO，进程不会傻等在那里，参考协程调度。
有些 IO 是没法一键协程化，没法将同步 IO 变为异步 IO 的，例如 MongoDB(相信 Swoole 会解决这个问题)，需要写代码时候注意。

借助底层的内置协程,swoole可以使用完全同步的代码来实现异步IO，PHP代码没有任何额外的关键字，底层会自动进行协程调度。

##### 同步 IO 转换成异步 IO

在 Swoole 下面，有些情况同步的 IO 操作是可以转换成异步 IO 的。

 - 开启一键协程化后，MySQL、Redis、Curl 等操作会变成异步 IO。
 - 利用 Event 模块手动管理事件，将 fd 加到 EventLoop 里面，变成异步 IO
 
 
##### Server 的两种运行模式介绍

在 Swoole\Server 构造函数的第三个参数，可以填 2 个常量值 -- SWOOLE_BASE 或 SWOOLE_PROCESS，下面将分别介绍这两个模式的区别以及优缺点

SWOOLE_PROCESS 进程模式的优点：

 - 连接与数据请求发送是分离的，不会因为某些连接数据量大某些连接数据量小导致 Worker 进程不均衡
 - Worker 进程发送致命错误时，连接并不会被切断
 - 可实现单连接并发，仅保持少量 TCP 连接，请求可以并发地在多个 Worker 进程中处理
 
SWOOLE_BASE 这种模式就是传统的异步非阻塞 Server。与 Nginx 和 Node.js 等程序是完全一致的。 
当有 TCP 连接请求进来的时候，所有的 Worker 进程去争抢这一个连接，并最终会有一个 worker 进程成功直接和客户端建立 TCP 连接，
之后这个连接的所有数据收发直接和这个 worker 通讯，**不经过主进程的 Reactor 线程转发**。

BASE 模式下没有 Master 进程的角色，只有 Manager 进程的角色。

每个 Worker 进程同时承担了 SWOOLE_PROCESS 模式下 Reactor 线程和 Worker 进程两部分职责。
BASE 模式下 Manager 进程是可选的，当设置了 worker_num=1，并且没有使用 Task 和 MaxRequest 特性时，
底层将直接创建一个单独的 Worker 进程，不创建 Manager 进程.

BASE 模式的优点：

 - BASE 模式没有 IPC 开销，性能更好
 - BASE 模式代码更简单，不容易出错
 
BASE 模式的缺点：

 - TCP 连接是在 Worker 进程中维持的，所以当某个 Worker 进程挂掉时，此 Worker 内的所有连接都将被关闭
 -  少量 TCP 长连接无法利用到所有 Worker 进程
 - TCP 连接与 Worker 是绑定的，长连接应用中某些连接的数据量大，这些连接所在的 Worker 进程负载会非常高。但某些连接数据量小，所以在 Worker 进程的负载会非常低，不同的 Worker 进程无法实现均衡。
 - 如果回调函数中有阻塞操作会导致 Server 退化为同步模式，此时容易导致 TCP 的 backlog 队列塞满问题。
 
BASE 模式的适用场景：

如果客户端连接之间不需要交互，可以使用 BASE 模式。如 Memcache、HTTP 服务器等。
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 