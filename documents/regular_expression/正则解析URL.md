# 正则匹配URL

URL客户端与服务端 按照相同的协议发送，接收

要匹配URL,首先需要了解URL的构成.

scheme:[//[user[:password]@]host[:port]][/path][?query][#fragment]

 scheme => protocol  协议
 hostname 域名
 port  端口
 path  路径
 parameters => query  参数
 fragment => anchor 片段
 
使用中括号包含的部分代表可以省略，省略时该部分按照默认值解析.

比如常见默认值:

 port : 80
 user : ''
 password：''

## URL的编码

URL编码遵循下列规则： 每对name/value由&；符分开；每对来自表单的name/value由=符分开。
如果用户没有输入值给这个name，那么这个name还是出现，只是无值。


任何特殊的字符（就是不能用ASCII编码表示的字符，如汉字）将以百分符%用十六进制编码进行传输，由服务端自行解析.像`=`,`&`；`，`和 `%` 这些特殊的字符可以用ASCII编码表示.(ASCII编码一共可以表示128种字符)

其实url编码就是一个字符ascii码的十六进制。不过稍微有些变动，需要在前面加上“%”。比如“\”，它的ascii码是92，92的十六进制是5c，所以“\”的url编码就是%5c。


	$str = "https://www.baidu.com/moudule/controller/action?id=1&name=2#33";
	preg_match_all("/(https?)\:\/\/([\w\.]+)\/([\w\/]+)(\??.*)/i",$str,$match);
	print_r($match);
