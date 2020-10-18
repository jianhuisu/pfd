## 5xx系列问题解决

解决5xx系列问题的首要手段就是查询nginx错误日志.

 - 500（服务器内部错误） 服务器遇到错误，无法完成请求。
 - 501（尚未实施）   服务器不具备完成请求的功能。例如，当服务器无法识别请求方法时，服务器可能会返回此代码。
 - 502  错误网关 bad gateway  上游有错误  php-fpm未启动或者不能正确的返回响应.
 - 503  service temporarily unavailable  一般情况下在出现Service Temporarily Unavailable错误多半是因为网站访问量过大造成的，当流量超限或者并发数大引起的资源超限出现的错误。一般情况当网站访问量过去之后网站就会恢复正常访
 - 504  网关超时 gateway time-out 上游超时   php-fpm执行超时
 - 505（HTTP 版本不受支持）    服务器不支持请求中所使用的 HTTP 协议版本。

我centos上nginx的error.log在var下

	[sujianhui@dev529 public]$>sudo tailf /var/log/nginx/error.log
	...
	2020/10/18 20:37:52 [error] 19271#19271: *19 connect() failed (111: Connection refused) while connecting to upstream, client: 127.0.0.1, server: local.laravel.com, request: "GET / HTTP/1.1", upstream: "fastcgi://127.0.0.1:9000", host: "local.laravel.com"
	2020/10/18 20:37:52 [crit] 19271#19271: *19 open() "/var/log/nginx/local.laravel.com.access.log" failed (13: Permission denied) while logging request, client: 127.0.0.1, server: local.laravel.com, request: "GET / HTTP/1.1", upstream: "fastcgi://127.0.0.1:9000", host: "local.laravel.com"

写的明明白白，上游没有响应.我一合计原来是php-fpm没启动....



