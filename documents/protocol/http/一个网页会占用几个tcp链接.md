# 一个网页会占用几个tcp链接

>现代浏览器在与服务器建立了一个TCP连接后是否会在一个 HTTP 请求完成后断开？什么情况下会断开？

 - HTTP1.0  请求完成后断开
 - HTTP1.1 `Connection: keep-alive (默认)`，浏览器和服务器之间是会维持一段时间的 TCP 连接，不会一个请求结束就断掉. `Connection: close` 请求完成后关闭连接.
 
https://blog.csdn.net/zuoxiaolong8810/article/details/65441709 
    
>一个 TCP 连接可以对应几个HTTP请求？

一个已经成功创建的TCP连接通道,可以发送多个HTTP请求.

 - HTTP1.1 两个请求的生命周期不能重叠，任意两个 HTTP 请求从开始到结束的时间在同一个 TCP 连接里不能重叠
 - HTTP2  可以一次性发送/接收多个HTTP请求

>浏览器对同一 Host 建立 TCP 连接到数量有没有限制？

 - 有。Chrome 最多允许对同一个 Host 建立六个 TCP 连接。不同的浏览器有一些区别。

>收到的HTML如果包含几十个图片标签，这些图片是以什么方式、什么顺序、建立了多少连接、使用什么协议被下载下来的呢？

 - HTTP1.1 浏览器就会在一个 HOST 上建立多个 TCP 连接，连接数量的最大限制取决于浏览器设置，这些连接会在空闲的时候被浏览器用来向同一HOST发送新的请求
 - HTTP2   可能会用到Multiplexing使用一个TCP链接发送

>https请求是否可以代理http请求

https请求可以代理一个http请求,https的加密解密位于应用层,不影响请求的`forwarded`.但是两者之间切换使用，会产生一些问题比如`session`数据共用起来会有问题. 

参考资料

https://zhuanlan.zhihu.com/p/61423830