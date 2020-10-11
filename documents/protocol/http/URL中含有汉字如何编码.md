# URL的编码

URL编码遵循下列规则： 

 - 每对`name/value`由`&`符分开；
 - 每对来自表单的`name/value`由`=`符分开.如果用户没有输入值给这个name，那么这个name还是出现，只是无值.
 
任何特殊的字符（就是不能用`ASCII`编码表示的字符，如`汉字`）将以百分符`%`+`十六进制编码`进行传输,由服务端自行解析.
像`=`,`&`；`，`和 `%` 这些特殊的字符可以用`ASCII`编码表示.(ASCII编码一共可以表示128种字符)

其实url编码就是一个字符ascii码的十六进制。
不过稍微有些变动，需要在前面加上`%`。比如`\`，它的ascii码是92，92的十六进制是5c，所以`\`的url编码就是`%5c`.

 1. 输入网址`http://zh.wikipedia.org/wiki/春节 `。注意，`春节`这两个字此时是网址路径的一部分。
 1. 查看HTTP请求的头信息，会发现实际查询的网址是`http://zh.wikipedia.org/wiki/%E6%98%A5%E8%8A%82 `。也就是说，Browser自动将`春节`编码成了`%E6%98%A5%E8%8A%82`。
 1. 我们知道，`春`和`节`的`utf-8`编码分别是`E6 98 A5`和`E8 8A 82`，因此，`%E6%98%A5%E8%8A%82`就是按照顺序，在每个字节前加上`%`而得到的。

pay attention to :

 1. 网址路径中包含汉字
 1. 查询字符串包含汉字
 1. Get方法生成的URL包含汉字
 1. Ajax调用的URL包含汉字

so. `GET`和`POST`方法的编码，用的是网页的编码
    
    <?php
	$str = "https://www.baidu.com/moudule/controller/action?id=1&name=2#33";
	preg_match_all("/(https?)\:\/\/([\w\.]+)\/([\w\/]+)(\??.*)/i",$str,$match);
	print_r($match);
