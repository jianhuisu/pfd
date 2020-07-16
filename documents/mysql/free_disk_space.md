## mysql的碎片整理

 1. MySQL官方建议不要经常(每小时或每天)进行碎片整理，一般根据实际情况，只需要每周或者每月整理一次即可。
 2. `OPTIMIZE TABLE`只对`MyISAM`，`BDB`和`InnoDB`表起作用，尤其是MyISAM表的作用最为明显。
 此外，并不是所有表都需要进行碎片整理，一般只需要对包含上述可变长度的文本数据类型的表进行整理即可。
 3. 在`OPTIMIZE TABLE`运行过程中，MySQL会锁定表。
 4. 默认情况下，直接对InnoDB引擎的数据表使用`OPTIMIZE TABLE`，可能会显示`Table does not support optimize, doing recreate + analyze instead`的提示信息。
 这个时候，我们可以用`mysqld --skip-new`或者`mysqld --safe-mode`命令来重启MySQL，以便于让其他引擎支持`OPTIMIZE TABLE`。

我们使用SQL删除数据后,数据是真的被删除了吗？

    drop table table_name 

立刻释放磁盘空间 ，不管是Innodb和MyISAM

    truncate table table_name
    
立刻释放磁盘空间 ，不管是 Innodb和MyISAM

    delete from table_name

删除表的全部数据，对于`MyISAM`会立刻释放磁盘空间,而InnoDB不会释放磁盘空间; 

    delete from table_name where xx 

带条件的删除, 不管是`innodb`还是`MyISAM`都不会释放磁盘空间

delete操作后使用`optimize table table_name`释放磁盘空间，优化表期间会锁定表，所以要在空闲时段执行.有人说:测试十几个G数据的表执行`optimize`大概20多分钟.

注：`delete`删除数据的时候，`mysql`并没有把数据文件删除，而是将数据文件的标识位删除，没有进行整理文件，因此不会彻底释放空间。
被删除的数据将会被保存在一个链接清单中，当有新数据写入的时候，mysql会利用这些已删除的空间再写入。

`OPTIMIZE TABLE tableName`命令优化表，该命令会重新利用未使用的空间，并整理数据文件的碎片；
该命令将会整理表数据和相关的索引数据的物理存储空间，用来减少占用的磁盘空间，并提高访问表时候的IO性能；
但是具体对表产生的影响是依赖于表使用的存储引擎的。该命令对视图无效。

使用`optimize table table_name`出现`Table does not support optimize, doing recreate + analyze instead`的解决办法：
另外可以通过对一些表进行压缩的方式来释放空间

#### 参考资料

原文链接：https://blog.csdn.net/hyfstyle/article/details/89141208