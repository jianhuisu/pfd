## ab压测工具笔记

    [root@zh888 bin]# /usr/local/apache/bin/ab -n 100 -c 100

表示同时处理100个请求并运行100次index.php文件.也就是同时启用本地机器的100个端口. 去连接远程服务器的服务端口.
简单的讲，就是一个会话.模拟多少客户端.理论上为同一时间点上发送到远程的请求数.

output.

    Benchmarking hostName (be patient)
    Completed 100 requests
    Completed 200 requests
    Completed 300 requests
    Completed 400 requests
    Completed 500 requests
    Completed 600 requests
    Completed 700 requests
    Completed 800 requests
    Completed 900 requests
    Completed 1000 requests
    Finished 1000 requests
    
    
    Server Software:        Tengine/2.1.2
    Server Hostname:        t-saas-dispatch.vhall.com
    Server Port:            443
    SSL/TLS Protocol:       TLSv1.2,ECDHE-RSA-AES256-GCM-SHA384,2048,256
    TLS Server Name:        t-saas-dispatch.vhall.com
    
    Document Path:          /v3/interacts/room/get-inav-tool-status
    Document Length:        105 bytes
    
    Concurrency Level:      10  并发数
    Time taken for tests:   9.496 seconds  所有请求耗时 单位秒
    Complete requests:      1000  成功请求次数
    Failed requests:        0     失败请求计数
    Total transferred:      473000 bytes
    HTML transferred:       105000 bytes
    Requests per second:    105.30 [#/sec] (mean)  并发为1时 相当于 我们常说的QPS/ 每秒事务数  //吞吐率，大家最关心的指标之一，相当于 LR 中的每秒事务数，后面括号中的 mean 表示这是一个平均值
    Time per request:       94.962 [ms] (mean)     平均请求响应时间 可以理解为一个socket链接的的占用时间,因为一个并发用户会占用一个socket链接,多个链接在这个链接上复用上，后面括号中的 mean 表示这是一个平均值  `concurrency * timetaken * 1000 / done`  用户平均请求等待时间，大家最关心的指标之二，相当于 LR 中的平均事务响应时间，后面括号中的 mean 表示这是一个平均值 
    Time per request:       9.496 [ms] (mean, across all concurrent requests)  每个请求实际运行时间的平均值  `timetaken * 1000 / done`  //服务器平均请求处理时间，大家最关心的指标之三
    Transfer rate:          48.64 [Kbytes/sec] received  平均每秒网络上的流量，可以帮助排除是否存在网络流量过大导致响应时间延长的问题 //平均每秒网络上的流量，可以帮助排除是否存在网络流量过大导致响应时间延长的问题
    
    Connection Times (ms)
                  min  mean[+/-sd] median   max
    Connect:       29   53  19.2     49     208  三次握手时间
    Processing:    21   41  10.4     40     127  近似服务器处理耗时
    Waiting:       21   40  10.2     39     127  近似服务器处理耗时
    Total:         60   94  22.9     89     249  请求响应耗时
    
    Percentage of the requests served within a certain time (ms)
      50%     89  50%的request可以在 89ms内完成.
      66%     93  ...
      75%     95
      80%     97
      90%    107
      95%    121
      98%    210
      99%    223
     100%    249 (longest request)
    
    Process finished with exit code 0

并发用户数（Concurrency Level）

要注意区分这个概念和并发连接数之间的区别，一个用户可能同时会产生多个会话，也即连接数。
在HTTP/1.1下，IE7支持两个并发连接，IE8支持6个并发连接，FireFox3支持4个并发连接，所以相应的，我们的并发用户数就得除以这个基数。
因为ab在处理时 默认一个并发用户创建一个并发连接. 所以 `Concurrency Level` 与 参数中的 `-n`值相同.


## 使用常见问题

 - 使用ab测试的时候当-c并发数超过1024就会出错.  ulimit -n 35768（设置系统允许同时打开的文件数，系统默认是1024）
 
## Question

 1. ApacheBench（ab）压测时两个Time per request 分表表示什么意思？
 
 - Time per request:       94.962 [ms] (mean)     
 - Time per request:       9.496 [ms] (mean, across all concurrent requests) 跨过并发概念进行的统计.
 
解释

 1. 平均请求响应时间 可以理解为一个socket链接的的占用时间,因为一个并发用户会占用一个socket链接,多个链接在这个链接上复用上，后面括号中的 mean 表示这是一个平均值  `concurrency * timetaken * 1000 / done`
    另一种理解:由于对于并发请求，cpu实际上并不是同时处理的，而是按照每个请求获得的时间片逐个轮转处理的，所以基本上第一个`Time per request`时间约等于第二个`Time per request`时间乘以并发请求数
    
 2. 每个请求实际运行时间的平均值 `timetaken * 1000 / done`
  