## 时间类型避坑指南


1.DATE、DATETIME和TIMESTAMP 表达的时间范围

|Type |	Range |	Remark |
|-----|-----|-----|
|DATE |	'1000-01-01' to '9999-12-31'  |	只有日期部分，没有时间部分 |
|DATETIME |	'1000-01-01 00:00:00' to '9999-12-31 23:59:59' |	时间格式为 YYYY-MM-DD hh:mm:ss，默认精确到秒 |
|TIMESTAMP |	 '1970-01-01 00:00:01' UTC to '2038-01-19 03:14:07'UTC	| 默认精确到秒 |


【Mysql】Datetime和Timestamp区别，及mysql中各种时间的使用
说到数据库时间类型，大部分同学都会想到date、datetime、timestamp之类的。

我之前在项目遇到一个问题，测试同事在测试时，由于会测试205几年的数据，在入库时会抛出数据库异常，
原因就是timestamp是有最大年份限制的。

下面先说说datetime与timestamp的区别：

#### 二者的字段存储值的格式不同

datetime的默认值为null，timestamp的默认值不为null，且为系统当前时间（current_timestatmp）。如果不做特殊处理，且update没有指定该列更新，则默认更新为当前时间。
datetime占用8个字节，timestamp占用4个字节。timestamp利用率更高。

#### 二者存储方式不一样

对于timestamp，它把客户端插入的时间从当前时区转化为世界标准时间（UTC）进行存储，查询时，逆向返回。
但对于datetime，基本上存什么是什么。

#### 二者范围不一样

timestamp范围：‘1970-01-01 00:00:01.000000’ 到 ‘2038-01-19 03:14:07.999999’；
datetime范围：’1000-01-01 00:00:00.000000’ 到 ‘9999-12-31 23:59:59.999999’。
原因是，timestamp占用4字节，能表示最大的时间毫秒为2的31次方减1，也就是2147483647， 换成时间刚好是2038-01-19 03:14:07.999999。

>>"timestamp占用4字节，能表示最大的时间毫秒为2的31次方减1，也就是2147483647，换成时间刚好是2038-01-19 03:14:07.999999。"
这里写错了吧，应该是能表示最大的时间的秒数是2147483647，而不是毫秒，2147483647/60/60/24/365+1970=2038.0962597349062，如果是毫秒，不是还要除以1000

### mysql存储时间戳

一、存入时间戳的问题
比如：通过JS获取到的时间戳是 1552448266077 ， 这种13位的时间戳，MySQL的字段类型，就不能设置为int了。

存入MySQL结果为 2147483647。
所以， create_time 字段类型可以设置为 varchar类型，长度13。

php的时间戳是10位的,所以可以直接用int来存储.

    [sujianhui@ saas-interact]$>php -r "echo time();"
    [sujianhui@ saas-interact]$>1611656718

它能表示的最大值为

    [sujianhui@ saas-interact]$>php -r "echo date('Y-m-d H:i:s',4294967295);";
    [sujianhui@ saas-interact]$>2106-02-07 14:28:15

**所以,我们还是乖乖的使用datetime来存储时间吧.**

### 参考资料

https://www.cnblogs.com/liuxs13/p/9760812.html
https://www.cnblogs.com/xuliuzai/p/10901425.html