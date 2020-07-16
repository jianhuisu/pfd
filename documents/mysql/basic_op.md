### 常见SQL

mysql查一张表有哪些索引
    
    mysql> show index from left_prefix;
    
Mysql 中！=和 <> 的区别

！=是以前sql标准，<>是现在使用的sql标准，推荐使用<>。

对现有表修改,追加索引.

    mysql> ALTER TABLE test_1 ADD INDEX index_code(code);