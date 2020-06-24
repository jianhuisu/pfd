## php的输出缓存区祥解

PHP的输出流包含很多字节，应为通常我们要获取PHP输出的文本，任何会输出点什么东西的函数都会用到输出缓冲区.例如使用以下输出函数/语法结构

 - echo
 - printf()
 - print_r()
 - var_dump()
 
在web应用环境中对php输出的内容使用缓冲区对性能有好处

##### 输出缓冲区的分层

两个抽象层之间的交互基本都是依靠缓冲层来实现的,就像我把苹果放在桌子上,然后你从桌子上拿苹果吃.
这个桌子就是缓冲区,苹果就是数据.所以缓冲区不是唯一的,php有缓冲区,nginx也有,c也有等等.各个抽象层的
缓冲区像栈一样堆叠在一起.例如我们很熟悉的LNMP套件中 php+nginx 的组合方式.

	
	OB层                php的缓冲层
	SAPI缓冲区层        php与其它服务交互的缓冲层  
	web服务器缓冲区     nginx服务接收php SAPI 接口数据的缓冲层

OB层/SAPI层都是PHP中的层.当输出的字节离开PHP进入计算机体系结构中的更底层时，缓冲区又会不断出现:terminal buffer，fast-cgi缓冲区，web服务器缓冲区，OS缓冲区，TCP/IP栈缓冲区...

注意:php缓冲区层的行为跟PHP使用的SAPI（fastcgi/cli)相关，不同的SAPI可能有不同的行为。


#### 缓冲区控制相关函数 参数

PHP三个跟缓冲区相关的INI配置选项：

 - output_buffering  `output_buffering=0`，这表示禁用输出缓冲区。若将值设为"ON"，默认的输出缓冲区的大小为16kb
 - implicit_flush    当`implicit_flush=1`，一旦有任何输出写入到SAPI缓冲区层，它都会立即flush（把这些数据写入到更低层，同时清空缓冲区）。eg. `php echo -> CLI SAPI`,`CLI SAPI`会立即将这些数据发送到stdout.
 - output_handler    output_handler是一个回调函数，它可以在缓冲区刷新之前修改缓冲区中的内容。

修改该参数后需要重启服务才能生效.

`implicit_flush`在CLI SAPI模式下设置为1,非CLI SAPI默认设置为off.

控制SAPI缓冲区函数

 - `flush()` 手动刷新SAPI的缓冲区. 对于FastCGI协议，刷新操作(flushing)是每次写入后都发送一个FastCGI数组包(packet)
 - `implicit_flush=1` 写一次就刷新一次 或者调用一次ob_implicit_flush()函数。
 
控制OB缓冲区函数

 - ob_flush()
 - 0b_implicit_flush()

output_handler是一个回调函数，它可以在缓冲区刷新之前修改缓冲区中的内容。
所以如果你想获取PHP传输给web服务器以及用户的内容，你可以使用输出缓冲区回调。

	ob_gzhandler : 使用ext/zlib压缩输出
	mb_output_handler : 使用ext/mbstring转换字符编码
	ob_iconv_handler : 使用ext/iconv转换字符编码
	ob_tidyhandler : 使用ext/tidy整理输出的HTML文本
	ob_[inflate/deflate]_handler : 使用ext/http压缩输出
	ob_etaghandler : 使用ext/http自动生成HTTP的Etag

**这里说的"输出"指的是消息头`headers`和消息体`body`。HTTP的消息头也是OB层的一部分。**
缓冲区中的内容会传递给你选择的回调函数（只能用一个）来执行内容转换的工作。
例如执行数据压缩，HTTP消息头管理以及搞很多其他的事情。

#### 为什么要有缓冲区

**缓冲区是为了防止出现过大量的细小的写入操作，从而造成访问SAPI层过于频繁，这样网络消耗会很大，不利于性能。**

##### 实战 

用户输出缓冲区，它通过调用ob_start()创建，我们可以创建很多这种缓冲区（至到内存耗尽为止），
这些缓冲区组成一个堆栈结构，每个新建缓冲区都会堆叠到之前的缓冲区上，每当它被填满或者溢出，都会执行刷新操作，然后把其中的数据传递给下一个缓冲区。

    ob_start(function($ctc) { static $a = 0; return $a++ . '- ' . $ctc . "\n";}, 10);
    ob_start(function($ctc) { return ucfirst($ctc); }, 3);
    echo "fo";
    sleep(2);
    echo 'o';
    sleep(2);
    echo "barbazz";
    sleep(2);
    echo "hello";
    /* 0- FooBarbazz\n 1- Hello\n */

在此我代替原作者讲解下这个示例。我们假设第一个ob_start创建的用户缓冲区为缓冲区1，第二个ob_start创建的为缓冲区2。按照栈的后进先出原则，任何输出都会先存放到缓冲区2中。
缓冲区2的大小为3个字节，所以第一个echo语句输出的字符串'fo'（2个字节）会先存放在缓冲区2中，还差一个字符，当第二echo语句输出的'o'后，缓冲区2满了，
所以它会刷新(flush)，在刷新之前会先调用ob_start()的回调函数，
这个函数会将缓冲区内的字符串的首字母转换为大写，所以输出为'Foo'。然后它会被保存在缓冲区1中，缓冲区1的大小为10。
第三个echo语句会输出'barbazz'，它还是会先放到缓冲区2中，这个字符串有7个字节，缓冲区2已经溢出了，所以它会立即刷新，
调用回调函数得到的结果为'Barbazz'，然后被传递到缓冲区1中。这个时候缓冲区1中保存了'FooBarbazz'，10个字符，缓冲区1会刷新，同样的先会调用ob_start()的回调函数，
缓冲区1的回调函数会在字符串前面添加行号，以及在尾部添加一个回车符，所以输出的第一行是'o- FooBarbazz'。

最后一个echo语句输出了字符串'hello'，它大于3个字符，所以会触发缓冲区2刷新，
因为此时脚本已执行完毕，所以也会立即刷新缓冲区1，最终得到的第二行输出为'1- Hello'。

##### from

http://gywbd.github.io/posts/2015/1/php-output-buffer-in-deep.html
