## 如何监测nginx并发连接数

#### 配置`status`模块监测

    location /status {
       stub_status on;
       # access_log /usr/local/nginx/logs/status.log;  
       # auth_basic "NginxStatus";            
    }
    
浏览器访问`http://127.0.0.1/status`
        
    Active connections: 4 
    server accepts handled requests
     5 5 32 
    Reading: 0 Writing: 1 Waiting: 3     
    
参数说明
    
 - `Active connections`         当前Nginx正处理的活动连接数,即并发连接数
 - `server accepts handledrequests`  总共处理了 387142个 连接, 成功创建 387142 次握手,总共处理了 4804888 个请求.(当启用`keep-alive`时,多个http请求会复用一个tcp连接)
 - `Reading`         nginx 读取到客户端的 Header 信息数.
 - `Writing`         nginx 返回给客户端的 Header 信息数.
 - `Waiting`         开启`keep-alive`的情况下,这个值等于`active-(reading+writing)`,意思就是Nginx已经处理完正在等候下一次请求指令的驻留连接.
 
监测结果应该监测的是整个nginx连接情况,而不仅仅局限于当前`vhost`.使用ab`yum install -y httpd-toolds`验证一下:`ab -c 10 -n 1000 http://local.pfd.com`

 - `-c 10` 表示并发用户数为`10`
 - `-n 1000` 表示请求总数为`1000`
 - `http://local.pfd.com\/`表示请求的目标URL,末尾需要以`\/`结束,否则报`ab: invalid URL`错误.

结果证明我刚才的猜想,访问其它`vhost`,当前`status`统计数据刷新.


#### 使用`netstat`命令监测

    netstat -n | awk '/^tcp/ {++S[$NF],print $NF} END {for(a in S) print a, S[a]}'  
    
并不是十分精确,可以做参考.

