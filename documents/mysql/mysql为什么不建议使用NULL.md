## MySQL不建议使用NULL作为列默认值到底为什么

通常能听到的答案是`使用了NULL值的列将会使所以失效`,但是如果实际测试过一下,你就知道`IS NULL`会使用索引.所以上述说法有漏洞.
但是毫无疑问,`NULL`是很特殊,很不合群的.既然NULL如此特殊，如此不合群,为什么它还会存在呢？我们什么时候需要使用NULL呢？

#### Preface 
 
>Null is a special constraint of columns.
The columns in table will be added null constrain if you do not define the column with "not null" key words explicitly 
when creating the table.Many programmers like to define columns by default 
because of the conveniences(reducing the judgement code of nullibility) what consequently 
cause some uncertainty of query and poor performance of database.
 
`NULL`值是一种对列的特殊约束,我们创建一个新列时,如果没有明确的使用关键字`not null`声明该数据列,`Mysql`会默认的为我们添加上`NULL`约束.
有些开发人员在创建数据表时,由于懒惰直接使用Mysql的默认推荐设置.(即允许字段使用`NULL`值).而这一陋习很容易在使用`NULL`的场景中得出不确定的查询结果以及引起数据库性能的下降.

#### Introduce

>Null is null means it is not anything at all,we cannot think of null is equal to '' and they are totally different.
MySQL provides three operators to handle null value:"IS NULL","IS NOT NULL","<=>" and a function ifnull().
IS NULL: It returns true,if the column value is null.
IS NOT NULL: It returns true,if the columns value is not null.
<=>: It's a compare operator similar with "=" but not the same.It returns true even for the two null values.
(eg. null <=> null is legal)
IFNULL(): Specify two input parameters,if the first is null value then returns the second one.
It's similar with Oracle's NVL() function.

`NULL`并不意味着什么都没有,我们要注意 `NULL` 跟 `''`(空值)是两个完全不一样的值.MySQL中可以操作`NULL`值操作符主要有三个.

 - `IS NULL`
 - `IS NOT NULL`
 - `<=>`  太空船操作符,这个操作符很像`=`,`select NULL<=>NULL`可以返回`true`,但是`select NULL=NULL`返回`false`.
 - `IFNULL` 一个函数.怎么使用自己查吧...反正我会了
 
Example
 
Null never returns true when comparing with any other values except null with "<=>".
`NULL`通过任一操作符与其它值比较都会得到`NULL`,除了`<=>`.

     1 (root@localhost mysql3306.sock)[zlm]>create table test_null(
     2     -> id int not null,
     3     -> name varchar(10)
     4     -> );
     5 Query OK, 0 rows affected (0.02 sec)
     6 
     7 (root@localhost mysql3306.sock)[zlm]>insert into test_null values(1,'zlm');
     8 Query OK, 1 row affected (0.00 sec)
     9 
    10 (root@localhost mysql3306.sock)[zlm]>insert into test_null values(2,null);
    11 Query OK, 1 row affected (0.00 sec)
    12 
    13 (root@localhost mysql3306.sock)[zlm]>select * from test_null;
    14 +----+------+
    15 | id | name |
    16 +----+------+
    17 |  1 | zlm  |
    18 |  2 | NULL |
    19 +----+------+
    20 2 rows in set (0.00 sec)
    21 // -------------------------------------->这个很有代表性<----------------------
    22 (root@localhost mysql3306.sock)[zlm]>select * from test_null where name=null;
    23 Empty set (0.00 sec)
    24 
    25 (root@localhost mysql3306.sock)[zlm]>select * from test_null where name is null;
    26 +----+------+
    27 | id | name |
    28 +----+------+
    29 |  2 | NULL |
    30 +----+------+
    31 1 row in set (0.00 sec)
    32 
    33 (root@localhost mysql3306.sock)[zlm]>select * from test_null where name is not null;
    34 +----+------+
    35 | id | name |
    36 +----+------+
    37 |  1 | zlm  |
    38 +----+------+
    39 1 row in set (0.00 sec)
    40 
    41 (root@localhost mysql3306.sock)[zlm]>select * from test_null where null=null;
    42 Empty set (0.00 sec)
    43 
    44 (root@localhost mysql3306.sock)[zlm]>select * from test_null where null<>null;
    45 Empty set (0.00 sec)
    46 
    47 (root@localhost mysql3306.sock)[zlm]>select * from test_null where null<=>null;
    48 +----+------+
    49 | id | name |
    50 +----+------+
    51 |  1 | zlm  |
    52 |  2 | NULL |
    53 +----+------+
    54 2 rows in set (0.00 sec)
    55  //null<=>null always return true,it's equal to "where 1=1".  

Null means "a missing and unknown value".Let's see details below.
NULL代表一个不确定的值,就算是两个NULL,它俩也不一定相等.(像不像C中未初始化的局部变量)


     1 (root@localhost mysql3306.sock)[zlm]>SELECT 0 IS NULL, 0 IS NOT NULL, '' IS NULL, '' IS NOT NULL;
     2 +-----------+---------------+------------+----------------+
     3 | 0 IS NULL | 0 IS NOT NULL | '' IS NULL | '' IS NOT NULL |
     4 +-----------+---------------+------------+----------------+
     5 |         0 |             1 |          0 |              1 |
     6 +-----------+---------------+------------+----------------+
     7 1 row in set (0.00 sec)
     8 
     9 //It's not equal to zero number or vacant string.
    10 //In MySQL,0 means fasle,1 means true.
    11 
    12 (root@localhost mysql3306.sock)[zlm]>SELECT 1 = NULL, 1 <> NULL, 1 < NULL, 1 > NULL;
    13 +----------+-----------+----------+----------+
    14 | 1 = NULL | 1 <> NULL | 1 < NULL | 1 > NULL |
    15 +----------+-----------+----------+----------+
    16 |     NULL |      NULL |     NULL |     NULL |
    17 +----------+-----------+----------+----------+
    18 1 row in set (0.00 sec)
    19 
    20 //It cannot be compared with number.
    21 //In MySQL,null means false,too.
    
It truns null as a result if any expression contains null value.
任何有返回值的表达式中有`NULL`参与时,都会得到另外一个`NULL`值.

     1 (root@localhost mysql3306.sock)[zlm]>select ifnull(null,'First is null'),ifnull(null+10,'First is null'),ifnull(concat('abc',null),'First is null');
     2 +------------------------------+---------------------------------+--------------------------------------------+
     3 | ifnull(null,'First is null') | ifnull(null+10,'First is null') | ifnull(concat('abc',null),'First is null') |
     4 +------------------------------+---------------------------------+--------------------------------------------+
     5 | First is null                | First is null                   | First is null                              |
     6 +------------------------------+---------------------------------+--------------------------------------------+
     7 1 row in set (0.00 sec)
     8 
     9   //null value needs to be disposed with ifnull() function,what usually causes sql statement more complex.
     10  //As we all know,MySQL does not support funcion index.Therefore,indexes on the column may not be used.That's really worse.
    

It's diffrent when using count(*) & count(null column).
使用`count(*)` 或者 `count(null column)`结果不同,`count(null column)`<=`count(*)`.


     1 (root@localhost mysql3306.sock)[zlm]>select count(*),count(name) from test_null;
     2 +----------+-------------+
     3 | count(*) | count(name) |
     4 +----------+-------------+
     5 |        2 |           1 |
     6 +----------+-------------+
     7 1 row in set (0.00 sec)
     8 
     9 //count(*) returns all rows ignore the null while count(name) returns the non-null rows in column "name".
    10 // This will also leads to uncertainty if someone is unaware of the details above.
     如果使用者对NULL属性不熟悉,很容易统计出错误的结果.

When using distinct,group by,order by,all null values are considered as the same value.
虽然`select NULL=NULL`的结果为`false`,但是在我们使用`distinct`,`group by`,`order by`时,`NULL`又被认为是相同`值`.

     1 (root@localhost mysql3306.sock)[zlm]>insert into test_null values(3,null);
     2 Query OK, 1 row affected (0.00 sec)
     3 
     4 (root@localhost mysql3306.sock)[zlm]>select distinct name from test_null;
     5 +------+
     6 | name |
     7 +------+
     8 | zlm  |
     9 | NULL |
    10 +------+
    11 2 rows in set (0.00 sec)
    12 
    13 //Two rows of null value returned one and the result became two.
    14 
    15 (root@localhost mysql3306.sock)[zlm]>select name from test_null group by name;
    16 +------+
    17 | name |
    18 +------+
    19 | NULL |
    20 | zlm  |
    21 +------+
    22 2 rows in set (0.00 sec)
    23 
    24 //Two rows of null value were put into the same group.
    25 //By default,group by will also sort the result(null row showed first).
    26 
    27 (root@localhost mysql3306.sock)[zlm]>select id,name from test_null order by name;
    28 +----+------+
    29 | id | name |
    30 +----+------+
    31 |  2 | NULL |
    32 |  3 | NULL |
    33 |  1 | zlm  |
    34 +----+------+
    35 3 rows in set (0.00 sec)
    36 
    37 //Three rows were sorted(two null rows showed first). 

MySQL supports to use index on column which contains null value(what's different from oracle).
MySQL中支持在含有`NULL`值的列上使用索引,但是`Oracle`不支持.这就是我们平时所说的如果列上含有`NULL`那么将会使索引失效.
严格来说,这句话对与MySQL来说是不准确的.

     1 (root@localhost mysql3306.sock)[sysbench]>show tables;
     2 +--------------------+
     3 | Tables_in_sysbench |
     4 +--------------------+
     5 | sbtest1            |
     6 | sbtest10           |
     7 | sbtest2            |
     8 | sbtest3            |
     9 | sbtest4            |
    10 | sbtest5            |
    11 | sbtest6            |
    12 | sbtest7            |
    13 | sbtest8            |
    14 | sbtest9            |
    15 +--------------------+
    16 10 rows in set (0.00 sec)
    17 
    18 (root@localhost mysql3306.sock)[sysbench]>show create table sbtest1\G
    19 *************************** 1. row ***************************
    20        Table: sbtest1
    21 Create Table: CREATE TABLE `sbtest1` (
    22   `id` int(11) NOT NULL AUTO_INCREMENT,
    23   `k` int(11) NOT NULL DEFAULT '0',
    24   `c` char(120) NOT NULL DEFAULT '',
    25   `pad` char(60) NOT NULL DEFAULT '',
    26   PRIMARY KEY (`id`),
    27   KEY `k_1` (`k`)
    28 ) ENGINE=InnoDB AUTO_INCREMENT=100001 DEFAULT CHARSET=utf8
    29 1 row in set (0.00 sec)
    30 
    31 (root@localhost mysql3306.sock)[sysbench]>alter table sbtest1 modify k int null,modify c char(120) null,modify pad char(60) null;
    32 Query OK, 0 rows affected (4.14 sec)
    33 Records: 0  Duplicates: 0  Warnings: 0
    34 
    35 (root@localhost mysql3306.sock)[sysbench]>insert into sbtest1 values(100001,null,null,null);
    36 Query OK, 1 row affected (0.00 sec)
    37 
    38 (root@localhost mysql3306.sock)[sysbench]>explain select id,k from sbtest1 where id=100001;
    39 +----+-------------+---------+------------+-------+---------------+---------+---------+-------+------+----------+-------+
    40 | id | select_type | table   | partitions | type  | possible_keys | key     | key_len | ref   | rows | filtered | Extra |
    41 +----+-------------+---------+------------+-------+---------------+---------+---------+-------+------+----------+-------+
    42 |  1 | SIMPLE      | sbtest1 | NULL       | const | PRIMARY       | PRIMARY | 4       | const |    1 |   100.00 | NULL  |
    43 +----+-------------+---------+------------+-------+---------------+---------+---------+-------+------+----------+-------+
    44 1 row in set, 1 warning (0.00 sec)
    45 
    46 (root@localhost mysql3306.sock)[sysbench]>explain select id,k from sbtest1 where k is null;
    47 +----+-------------+---------+------------+------+---------------+------+---------+-------+------+----------+--------------------------+
    48 | id | select_type | table   | partitions | type | possible_keys | key  | key_len | ref   | rows | filtered | Extra                    |
    49 +----+-------------+---------+------------+------+---------------+------+---------+-------+------+----------+--------------------------+
    50 |  1 | SIMPLE      | sbtest1 | NULL       | ref  | k_1           | k_1  | 5       | const |    1 |   100.00 | Using where; Using index |
    51 +----+-------------+---------+------------+------+---------------+------+---------+-------+------+----------+--------------------------+
    52 1 row in set, 1 warning (0.00 sec)
    53 
    54 //In the first query,the newly added row is retrieved(检索) by primary key.
    55 //In the second query,the newly added row is retrieved by secondary key "k_1"
    56 // It has been proved that indexes can be used on the columns which contain null value.
       通过explain 可以看到 mysql支持含有NULL值的列上使用索引 
    57 //column "k" is int datatype which occupies 4 bytes,but the value of "key_len" turn out to be 5.
       // what's happed?Because null value needs 1 byte to store the null flag in the rows.

这个是我自己测试的例子.

    mysql> select * from test_1;
    +-----------+------+------+
    | name      | code | id   |
    +-----------+------+------+
    | gaoyi     | wo   |    1 |
    | gaoyi     | w    |    2 |
    | chuzhong  | wo   |    3 |
    | chuzhong  | w    |    4 |
    | xiaoxue   | dd   |    5 |
    | xiaoxue   | dfdf |    6 |
    | sujianhui | su   |   99 |
    | sujianhui | NULL |   99 |
    +-----------+------+------+
    8 rows in set (0.00 sec)

    mysql> explain select * from test_1 where code is NULL;
    +----+-------------+--------+------------+------+---------------+------------+---------+-------+------+----------+-----------------------+
    | id | select_type | table  | partitions | type | possible_keys | key        | key_len | ref   | rows | filtered | Extra                 |
    +----+-------------+--------+------------+------+---------------+------------+---------+-------+------+----------+-----------------------+
    |  1 | SIMPLE      | test_1 | NULL       | ref  | index_code    | index_code | 161     | const |    1 |   100.00 | Using index condition |
    +----+-------------+--------+------------+------+---------------+------------+---------+-------+------+----------+-----------------------+
    1 row in set, 1 warning (0.00 sec)
    
    mysql> explain select * from test_1 where code is not NULL;
    +----+-------------+--------+------------+-------+---------------+------------+---------+------+------+----------+-----------------------+
    | id | select_type | table  | partitions | type  | possible_keys | key        | key_len | ref  | rows | filtered | Extra                 |
    +----+-------------+--------+------------+-------+---------------+------------+---------+------+------+----------+-----------------------+
    |  1 | SIMPLE      | test_1 | NULL       | range | index_code    | index_code | 161     | NULL |    7 |   100.00 | Using index condition |
    +----+-------------+--------+------------+-------+---------------+------------+---------+------+------+----------+-----------------------+
    1 row in set, 1 warning (0.00 sec)
    
    mysql> explain select * from test_1 where code='dd';
    +----+-------------+--------+------------+------+---------------+------------+---------+-------+------+----------+-----------------------+
    | id | select_type | table  | partitions | type | possible_keys | key        | key_len | ref   | rows | filtered | Extra                 |
    +----+-------------+--------+------------+------+---------------+------------+---------+-------+------+----------+-----------------------+
    |  1 | SIMPLE      | test_1 | NULL       | ref  | index_code    | index_code | 161     | const |    1 |   100.00 | Using index condition |
    +----+-------------+--------+------------+------+---------------+------------+---------+-------+------+----------+-----------------------+
    1 row in set, 1 warning (0.00 sec)
    
    mysql> explain select * from test_1 where code like "dd%";
    +----+-------------+--------+------------+-------+---------------+------------+---------+------+------+----------+-----------------------+
    | id | select_type | table  | partitions | type  | possible_keys | key        | key_len | ref  | rows | filtered | Extra                 |
    +----+-------------+--------+------------+-------+---------------+------------+---------+------+------+----------+-----------------------+
    |  1 | SIMPLE      | test_1 | NULL       | range | index_code    | index_code | 161     | NULL |    1 |   100.00 | Using index condition |
    +----+-------------+--------+------------+-------+---------------+------------+---------+------+------+----------+-----------------------+
    1 row in set, 1 warning (0.00 sec)


#### Summary 总结

>null value always leads to many uncertainties when disposing sql statement.It may cause bad performance accidentally.

**列中使用`NULL`值容易引发不受控制的事情发生,有时候还会严重托慢系统的性能.**

例如:
 
 - null value will not be estimated in aggregate function() which may cause inaccurate results.
   对含有NULL值的列进行统计计算,eg. `count()`,`max()`,`min()`,结果并不符合我们的期望值.
 - null value will influence the behavior of the operations such as "distinct","group by","order by" which causes wrong sort.
   干扰排序，分组,去重结果.
 - null value needs ifnull() function to do judgement which makes the program code more complex.
   有的时候为了消除`NULL`带来的技术债务,我们需要在SQL中使用`IFNULL()`来确保结果可控,但是这使程序变得复杂.
 - null value needs a extra 1 byte to store the null information in the rows.
    **`NULL`值并是占用原有的字段空间存储,而是额外申请一个字节去标注,这个字段添加了`NULL`约束.(就像额外的标志位一样)**

>As these above drawbacks,it's not recommended to define columns with default null.
We recommand to define "not null" on all columns and use zero number & vacant string to substitute relevant data type of null.

根据以上缺点,我们并不推荐在列中设置NULL作为列的默认值,你可以使用`NOT NULL`消除默认设置,使用`0`或者`''`空字符串来代替`NULL`.

#### 参考资料

原文地址 https://www.cnblogs.com/aaron8219/p/9259379.html











