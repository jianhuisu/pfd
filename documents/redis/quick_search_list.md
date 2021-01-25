# 速查表

 - Dbsize 返回当前数据库的 key 的数量
 - Type key  查看key的类型
 - Llen key 查看List的长度
 - lrange key 0 -1 查看list中所有的key
 - SMEMBERS myset1  返回集合中的所有的成员
 - SCARD KEY_NAME
 - hgetall KEY_NAME  
 - hlen KEY_NAME
 - HKEYS myhash
 - Client list 查看维持的客户端连接
 - ttl 查看当前key剩余的存活时间 
 - Echo  输出一个字符串 不知道这个有什么用. 
 - Select  切库
 - Ping  使用客户端向 Redis 服务器发送一个 PING ，如果服务器运作正常的话，会返回一个 PONG
 - Quit  命令用于关闭与当前客户端与redis服务的连接。一旦所有等待中的回复(如果有的话)顺利写入到客户端，连接就会被关闭。
 - Auth  


