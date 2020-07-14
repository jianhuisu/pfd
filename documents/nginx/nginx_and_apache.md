
nginx以 epoll/kqueue 作为开发模型，处理请求是异步非阻塞的，负载能力比apache高很多，而apache则是select同步阻塞

nginx master-worker

apache是同步多进程模型，一个连接对应一个进程，而nginx是异步的，多个连接（万级别）可以对应一个进程。

apache工作模式：

beos工作模式（跟linux关系不大，或者暂时用不上）

prefork工作模式（本篇文章的主角，使用最多而且最稳定的工作模式）

apache和nginx对比
 
Nginx相对于Apache：

1、高并发响应性能非常好。（单台万级并发连接30000-50000/s（简单静态页））
2、反向代理性能非常好。（可用于负载均衡）
3、内存和cpu占用率低。（为Apache的1/5-1/10）
4、功能较Apache少（常用功能均有）
5、对php可使用cgi方式和fastcgi方式，没有模块编译加载方式。