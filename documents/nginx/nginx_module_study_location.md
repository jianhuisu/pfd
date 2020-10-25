# nginx模块学习_location

## `lcation`语法规则

	location [=|~|~*|^~] /uri/ {
		...
	}

`location`后接的匹配规则含义

 - `=`     表示精确匹配.相当于php中的`==`
 - `^~`    匹配uri中的path部分以某个常规字符串开头的url即可.
 - `~`     区分大小写的正则匹配
 - `~*`    不区分大小写的正则匹配
 - `!~`    区分大小写不匹配的正则
 - `!~*`   不区分大小写不匹配的正则
 - `/`     通用匹配，任何请求都会匹配到

当我们有多个`location`配置的情况下，其匹配顺序优先级为：

 1. 首先匹配 `=`，
 1. 其次匹配 `^~`, 其次是按文件中顺序的正则匹配，
 1. 最后是交给 `/` 通用匹配

总结来说,模式精准度级别(可以理解一共有三种level:精准level.正则level.通用level)越高,匹配的优先级越高.当有匹配成功时候，停止匹配，按当前匹配规则处理请求.

## 匹配规则实战.

现有vhost.conf内容如下

	location = / {
	   #规则A
	}

	location = /login {
	   #规则B
	}

	location ^~ /static/ {
	   #规则C
	}

	location ~ \.(gif|jpg|png|js|css)$ {
	   #规则D
	}

	location ~* \.png$ {
	   #规则E
	}

	location !~ \.xhtml$ {
	   #规则F
	}

	location !~* \.xhtml$ {
	   #规则G
	}

	location / {
	   #规则H
	}


下列场景:

 1. 访问url的path为`/`比如`http://localhost/` 将匹配规则`A`
 2. 访问url`http://localhost/login`,将匹配规则`B`，`http://localhost/register`.则匹配规则`H` (这个后边验证以下)
 3. 访问 `http://localhost/static/a.html`,将匹配规则`C`
 4. 访问 `http://localhost/a.gif`, `http://localhost/b.jpg` . **规则`D`和规则`E`符合匹配规则，但是规则D顺序优先，规则E不起作用**.
 5. 访问 `http://localhost/a.PNG`,  则匹配规则`E`，而不会匹配规则`D`，因为规则`E`不区分大小写.
 6. 访问 `http://localhost/a.xhtml` ,不会匹配规则F和规则G.
 7. 访问 `http://localhost/category/id/1111`. 则最终匹配到规则`H`，因为以上规则都不匹配，这个时候应该是`nginx`转发请求给后端应用服务器，比如F`astCGI（php）`，`tomcat（jsp）`，`nginx`作为方向代理服务器存在.
	
上边除了`2`，`7`应该都没有什么问题.按照我的理解.nginx在第一次解析该conf时，因该会对每个`location block`进行一次稳定排序.当稳定运行时直接按照稳定排序的结果进行匹配即可.所以当其它规则没有匹配成功时.我认为生效的应该是规则`A`而不是规则`H`.所以规则`H`与规则`A`到底谁优先生效需要验证一下.我编写了如下`conf`

	[sujianhui@dev529 conf.d]$>cat test.conf 
	server {
	    listen       80;
	    server_name  local.test.com;

	    location / {
		root   /home/sujianhui/PhpstormProjects/blog;
		index  composer.json;
	    }

		location ^~ /static {
			root /home/sujianhui/PhpstormProjects/blog;
			index server.php;
		}


		location / {
			root /home/sujianhui/PhpstormProjects/blog;
			index README.md;
		}	

	}


然后,重启失败.

	[sujianhui@dev529 conf.d]$>tail -n 10 /var/log/nginx/error.log
	2020/10/25 15:05:59 [emerg] 7719#7719: duplicate location "/" in /etc/nginx/conf.d/test.conf:16	

**所以,同一个conf中不能同时出现两个统配符,上边那种情况不会出现**.我的nginx版本是.`nginx/1.18.0`.可能跟版本有关系吧.

另外在实际的应用场景中.有三个location是必不可少的.

第一个必选规则:统配符

	location = / {
	    proxy_pass http://localhost:8080/index
	}

直接匹配网站根，因为通过域名访问网站首页比较频繁，这里是直接转发给后端应用服务器了，更好的选择是一个静态首页.使用这个会加速处理，官网如是说.

第二个必选规则:动静分离	

	location ^~ /static/ {
	    root /webroot/static/;
	}
	
	# 或者如何格式,根据自己的场景选择

	location ~* \.(gif|jpg|jpeg|png|css|js|ico)$ {
	    root /webroot/res/;
	}


这个场景也很有意思,我用yii2.0框架的时候,因为`vhost.conf`没有配全第二种格式,导致很多的静态资源请求也被转发到`php框架`这边当作动态请求处理了.
结果当然是路由解析失败了.大量的这种异常信息填满了网站日志.浪费空间与精力.上边两种格式根据自己的业务场景选择吧.

第三个必选规则.转发动态请求到后端应用服务器

	location / {
	    	fastcgi_pass   127.0.0.1:9000;
		fastcgi_index  index.php;
		fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
		include        fastcgi_params;
	}

毕竟目前的一些框架的推行`url美化`，`伪静态`.导致`url`中带`.php`, `.jsp` 后缀的情况很少了.所以一般情况我们都需要转发所有非静态请求到应用服务器.
`http://localhost/index.php?module=user&controller=sign&action=login` 都已经转化为`http://localhost/user/sign/action`.由框架中的路由器按照路由协议进行解析.不过这个转发动态请求的用法各有不同.下面是yaf框架的一种搭配使用方法.
	
	if (!-e $request_filename) {
		rewrite ^/(.*)  /index.php?$1 last;
	}

	location ~ \.php$ {
		fastcgi_pass   127.0.0.1:9000;
		fastcgi_index  index.php;
		fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
		include        fastcgi_params;
	}


Tips:补充一下

`rewrite`语法

 - `last`       – 基本上都用这个 Flag.与break作用基本相同.
 - `break`      – 中止 Rewirte，不在继续匹配
 - `redirect`   – 返回临时重定向的HTTP状态302
 - `permanent`  – 返回永久重定向的HTTP状态301

可以用来判断的表达式

 - `-f` 和 `!-f`    用来判断是否存在文件
 - `-d` 和 `!-d`    用来判断是否存在目录
 - `-e` 和 `!-e`    用来判断是否存在文件或目录
 - `-x` 和 `!-x`    用来判断文件是否可执行

## 一些常用配置

防盗链

	location ~* \.(gif|jpg|swf)$ {
	    valid_referers none blocked start.igrow.cn sta.igrow.cn;
	    if ($invalid_referer) {
		rewrite ^/ http://$host/logo.png;
	    }
	}

Redirect语法

	server {
	    listen 80;
	    server_name start.igrow.cn;
	    index index.html index.php;
	    root html;
	    if ($http_host !~ “^star\.igrow\.cn$&quot {
		rewrite ^(.*) http://star.igrow.cn$1 redirect;
	    }
	}

设置静态资源的缓存时间

	location ~* \.(js|css|jpg|jpeg|gif|png|swf)$ {
	    if (-f $request_filename) {
		expires 1h;
		break;
	    }
	}

禁止访问某个目录

	location ~* \.(txt|doc)${
	    root /data/www/wwwroot/linuxtone/test;
	    deny all;
	}

禁止记录日志

	location = /favicon.ico {
		
		log_not_found off;
		access_log off;
	}

## 一些配置文件中可用的全局变量

	$args
	$content_length
	$content_type
	$document_root
	$document_uri
	$host
	$http_user_agent
	$http_cookie
	$limit_rate
	$request_body_file
	$request_method
	$remote_addr
	$remote_port
	$remote_user
	$request_filename
	$request_uri
	$query_string
	$scheme
	$server_protocol
	$server_addr
	$server_name
	$server_port
	$uri

## 原文地址

https://www.cnblogs.com/paul8339/p/11328459.html 稍作修改补充


