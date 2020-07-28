### 常见SQL

mysql查一张表有哪些索引
    
    mysql> show index from left_prefix;
    
Mysql 中！=和 <> 的区别

！=是以前sql标准，<>是现在使用的sql标准，推荐使用<>。

对现有表修改,追加索引.

    mysql> ALTER TABLE test_1 ADD INDEX index_code(code);
    
    
查看表的基本状态(默认查看当前库中所有表的状态)

    show table status\G    
    
查看全局或者当前会话的计数    
    
    mysql> flush status;    
    mysql> show status where variable_name like 'handler%' or variable_name like 'created%';
    mysql> show session status where variable_name like 'handler%' or variable_name like 'created%';
    mysql> show global status where variable_name like 'handler%' or variable_name like 'created%';
        