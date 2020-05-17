## ZTS and NTS  

`TS` (Thread-Safety)即线程安全，多线程访问时，采用了加锁机制，当一个线程访问该类的某个数据时，进行保护，其他线程不能进行访问直到该线程读取完，其他线程才可使用，不会出现数据不一致或者数据污染。
`NTS`(None-Thread Safe)即非线程安全，就是不提供数据访问保护，有可能出现多个线程先后更改数据造成所得到的是脏数据php以fast cgi方式运行的时候选择这个版本，具有更好的性能。

### PHP常见的四种运行模式

`SAPI（Server Application Programming Interface）`服务器应用程序编程接口，即PHP与其他应用交互的接口.
PHP脚本要执行有很多方式，通过Web服务器，或者直接在命令行下，也可以嵌入在其他程序中。
`SAPI`提供了一个和外部通信的接口，常见的`SAPI`有：`cgi`、`fast-cgi`、`cli`、`isapi` apache模块的DLL
 
 1. `ISAPI`模式  以web服务器的一个模块加载运行,其实就是将PHP的源码与webServer的代码一起编译，运行时是同一个进程,共享同一个地址空间. 例如 LAMP中,PHP就是作为Apache的一个模块运行的.Apache是多线程调用php模块的.(same as IIS)
 1. `CGI`模式  `fork-and-execute` webServer将动态请求转发到CGI程序(以php为例子),就相当于fork一个子进程,然后`exec(php process)`,用CGI程序来解释请求内容,最后将子进程的`output`返回.此时webServer与php进程的地址空间是独立的.此时的php是作为一个独立的程序运行.
 1. `FastCGI`模式 这种形式是CGI的加强版本，CGI是单进程，多线程的运行方式，程序执行完成之后就会销毁，所以每次都需要加载配置和环境变量（创建-执行）。
   而FastCGI则不同，FastCGI 是一个常驻 (long-live) 型的 CGI，它可以一直执行着，只要激活后，不会每次都要花费时间去 fork 一次。
 1. `CLI`

#### choose 

 - `Apache`是同步多进程模型，一个连接对应一个进程.(?? todo 每个请求都会独占一个工作线程 )
 - `Nginx`是异步多进程模型，多个连接（万级别）可以对应一个进程.异步非阻塞的事件处理机制.

so 

 - Linux Nginx + PHP , 选择`PHP NTS VERSION`
 - Linux Apache + PHP (CGI mode)  选择`PHP NTS VERSION`
 - Linux Apache + PHP (ISAPI mode) 选择`ZTS VERSION`
