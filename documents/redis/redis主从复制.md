# redis 主从复制实现 

Redis主从复制主要分为`全量同步`和`增量同步`.

1. 全量同步

Redis全量复制一般发生在Slave初始化阶段，这时Slave需要将Master上的所有数据都复制一份。

 1. slave 向master 发送 sync命令
 2. master向接收到sync命令后执行 BGSave. 生成RDB. 
 3. RDB发送到slave，slave载入RDB
 4. master向slave缓冲区发送写命令,
 5. slvae缓冲区执行写命令.


2. 增量同步

Redis增量复制是指Slave初始化后开始正常工作时主服务器发生的写操作同步到从服务器的过程。
增量复制的过程主要是主服务器每执行一个写命令就会向从服务器发送相同的写命令，从服务器接收并执行收到的写命令。