## mysql表空间满了怎么处理

#### id达到上限 

    Duplicate entry '4294967295' for key 'PRIMARY'
    
    show create table `tableName`;
    CREATE TABLE `tableName` (
      `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
      `count` bigint(20) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

解决办法

    增大id最大值，修改为bigint(20).
    并设置自增：alter table `tableName` AUTO_INCREMENT=4294967296;
    
为什么需要主键

 - 主键的作用，在于索引.
 - 主键的列值是不能重复的
 - 不应该使用一个具有意义的column（id 本身并不保存表 有意义信息）作为主键，**因为主键不适合更新，主键适合生成后就不会改变**.
     
#### 参考资料

原文链接：https://blog.csdn.net/ddxygq/article/details/102481611