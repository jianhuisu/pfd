

redis的三种集群方式

 1. 主从模式: 主从模式的弊端就是不具备高可用性,当主节点挂掉后,redis将不能在对外提供写入操作
 2. redis哨兵(Sentinel)模式 :  
 3. Cluster模式 :


redis 的key是如何寻址的 => hash slot 算法. 分配对应slot服务器上.