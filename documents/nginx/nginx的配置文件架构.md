# nginx配置文件主要分为六个区域： 

 - 1、main      (全局设置)
 - 2、events    (nginx工作模式)
 - 3、http      (http设置)
 - 4、sever     (主机设置)
 - 5、location  (URL匹配)
 - 6、upstream  (负载均衡服务器设置)
 
## 2.1 main模块

下面是一个main区域，他是一个全局的设置

    user nobody nobody;              # 指定 Nginx Worker 进程运行用户以及用户组，默认由 nobody 账号运行
    worker_processes 2;              # 指定 Nginx 要开启的子进程数
    error_log  /usr/local/var/log/nginx/error.log  notice;      # 定义全局错误日志文件
    pid        /usr/local/var/run/nginx/nginx.pid;              # 指定进程 id 的存储文件位置
    worker_rlimit_nofile 1024;       # 指定一个 nginx 进程可以打开的最多文件描述符数目，如果设置 65535，需要使用命令 “ulimit -n 65535” 来设置
    
user 来指定 Nginx Worker 进程运行用户以及用户组，默认由 nobody 账号运行。

worker_processes 来指定了 Nginx 要开启的子进程数。每个 Nginx 进程平均耗费 10M~12M 内存。根据经验，一般指定 1 个进程就足够了，如果是多核 CPU，建议指定和 CPU 的数量一样的进程数即可。我这里写 2，那么就会开启 2 个子进程，总共 3 个进程。

error_log 用来定义全局错误日志文件。日志输出级别有 debug、info、notice、warn、error、crit 可供选择，其中，debug 输出日志最为最详细，而 crit 输出日志最少。

pid 用来指定进程id的存储文件位置。

worker_rlimit_nofile 用于指定一个 nginx 进程可以打开的最多文件描述符数目，这里是 65535，需要使用命令 “ulimit -n 65535” 来设置。

## 2.2 events 模块

events 模块来用指定 nginx 的工作模式和工作模式及连接数上限，一般是这样

    events {
        use kqueue;                # mac 平台，指定 Nginx 的工作模式
        worker_connections  1024;  # 定义 Nginx 每个进程的最大连接数，即接收前端的最大请求数，默认是 1024
    }
    
use 用来指定 Nginx 的工作模式。Nginx 支持的工作模式有 select、poll、kqueue、epoll、rtsig 和 /dev/poll。其中 select 和 poll 都是标准的工作模式，kqueue 和 epoll 是高效的工作模式，不同的是 epoll 用在 Linux 平台上，而 kqueue 用在 BSD 系统中，因为 Mac 基于 BSD ,所以 Mac 也得用这个模式，对于 Linux 系统，epoll 工作模式是首选。

worker_connections 用于定义Nginx每个进程的最大连接数，即接收前端的最大请求数，默认是1024。最大客户端连接数由worker_processes 和 worker_connections 决定，即 Max_clients = worker_processes * worker_connections，在作为反向代理时，Max_clients 变为：Max_clients = worker_processes * worker_connections/4。 
进程的最大连接数受 Linux 系统进程的最大打开文件数限制，在执行操作系统命令 “ulimit -n 65536” 后 worker_connections 的设置才能生效。

 
https://my.oschina.net/u/3314358/blog/1836822
