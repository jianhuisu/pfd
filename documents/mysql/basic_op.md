### 常见SQL

查看mysql启动时,加载my.cnf的顺序

    [sujianhui@dev0529 ~]$>mysql --help | grep my.cnf
                          order of preference, my.cnf, $MYSQL_TCP_PORT,
    /etc/my.cnf /etc/mysql/my.cnf /usr/etc/my.cnf ~/.my.cnf 

mysql在启动时如果不能找到my.cnf，会加载编译时设置的默认配置项.而不是报错停止启动.

模拟随机数据:生成随机初始数据(多次执行)

    mysql> insert into user(name,email) values(concat_ws('_','sujianhui',ROUND(RAND() * 5444 + 234)),concat_ws('',ROUND(RAND() * 5444 + 234), '@qq.com'));

模拟随机数据:扩散随机数据(多次执行),
    
    mysql> insert into test_page(name,email) select name,email from test_page order by rand();
    // 这个不是分组，只是排序，rand()只是生成一个随机数。 ORDER By rand()，这样每次检索的结果排序会不同
    
        

建表语句

    CREATE TABLE `tbl` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `uid` int unsigned NOT NULL DEFAULT '0',
      `title` char(50) NOT NULL DEFAULT '',
      `score` tinyint unsigned NOT NULL DEFAULT '0',
      PRIMARY KEY (`id`),
      KEY `uid` (`uid`,`score`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
    
    create table user(
        id int unsigned not null auto_increment,
        name char(50) not null default '',
        email char(50) not null default '',
        primary key(id),
        index name(name),
        index name_em(name,email)
    )engine=innodb;
    
mysql查一张表有哪些索引
    
    mysql> show index from left_prefix;
    
Mysql 中！=和 <> 的区别

    !=是以前sql标准，<>是现在使用的sql标准，推荐使用<>。

对现有表修改,追加普通索引.

    mysql> ALTER TABLE test_1 ADD INDEX index_code(code);

对现有表修改,追加主键索引.

    mysql> alter table test_page add primary key id(id);

对现有表修改,修改字段类型.
    
    mysql> alter table test_page modify column id int(11) unsigned not null auto_increment [primary key];

修改表,增加列    
    
    mysql> alter table test_page add column id int(11) unsigned not null auto_increment primary key;

修改表,删除列(会自动删除索引)    
    
    mysql> alter table test_page drop column columnName;
    
重命名表
    
    mysql>rename table film_actor to actor;    
    
查看表的基本状态(默认查看当前库中所有表的状态)

    show table status\G    
    
查看全局或者当前会话的计数    
    
    mysql> flush status;    
    mysql> show status where variable_name like 'handler%' or variable_name like 'created%';
    mysql> show session status where variable_name like 'handler%' or variable_name like 'created%';
    mysql> show global status where variable_name like 'handler%' or variable_name like 'created%';

group by

    select name,group_concat(id) from user group by name

rollup
    
    mysql> SELECT
    ->  coalesce(business_name,'总计')
    ->  business_name,
    ->  sum(work_level_1_xiangmuhexin),
    ->  sum(work_level_1_totalize)
    ->  FROM
    ->  middle_org_work_level1
    ->  WHERE date='2019-07-10' 
    ->  group by business_name with ROLLUP;
    
    +--------------------+--------------------------------+----------------------------+
    | business_name      | sum(work_level_1_xiangmuhexin) | sum(work_level_1_totalize) |
    +--------------------+--------------------------------+----------------------------+
    | 中端                |                             26 |                        545 |
    | 前端运营纵线         |                              0 |                        113 |
    ...
    | 总计                |                            231 |                       5978 |
    +--------------------+--------------------------------+----------------------------+            