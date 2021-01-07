# nginx_rewrite之后url中同时存在两个问号

一个正常的url `http://localhost/cmpt/document/list?name=sjh`

那么php接收到以后

    var_dump($_GET) 
    array(
        name => sjh
    )

很多php框架为了统一应用入口文件,都会在nginx中location模块中配置重写.现在nginx增加重写规则,将所有请求的入口统一为index.php

    location / {
        if (!-e $request_filename) {
            rewrite ^/(.*)  /index.php?$1 last;
        }
    }

    ...

    location ~ \.php(.*)$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }

按照我的理解,此时

- 原始url `http://localhost/cmpt/document/list?name=sjh`
- rewrite之后的url  `http://localhost/index.php?cmpt/document/list?name=sjh`

但是,这样同一个url中会存在两个`?`啊,那php如何从这个url解析出真实的`query_params`.

网上百度一下,有个网友如下解释：

1. nginx在进行rewrite的正则表达式中只会将url中？前面的部分拿出来匹配
2. 匹配完成后，？后面的内容将自动追加到url中（包含？），如果不让后面的内容追加上去，请在最后加上？即可
3. 如果要活的？后面的内容则请使用$query_string

>Tips:在这里提醒一点，调试的时候在rewrite的最后一个配置项中不要使用break last这些，使用redirect可以看到转换后的地址。

所以,勤快点我赶紧配置一波

    location / {
        if (!-e $request_filename) {
            rewrite ^/(.*)  /index.php?$1 redirect;
        }
    }

`sudo nginx -s reload` + 刷新页面,得到

- 原始url : `http://local.vss.vhall.com/cmpt/document/list?name=adadf`
- redirect Url : `http://local.vss.vhall.com/index.php?cmpt/document/list&name=adadf`

真的耶,两个`?`的部分被nginx合并了.php端打印的结果.

    array(
        'cmpt/document/list' => '',
        'name'  => 'adadf',
    )

那个网友真不赖.
 