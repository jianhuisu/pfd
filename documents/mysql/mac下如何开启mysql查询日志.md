# mac 下如何开启mysql查询日志

laravel框架里面记录的sql日志参数是占位符+绑定参数 格式的. 自行替换之后发现where条件中一些int参数被当做字符串条件使用

    常规sql ：      where uid=1
    laravel sql ： where uid='1' 

隐式类型转换会有额外的性能开销,但是之前一直没有人反馈过这个问题,我也拿不准.不太适合直接问别人.自己监测一下发送到mysql服务端执行的sql到底长什么样.
所以打一个常规查询的日志.    

## mysql日志

mysql(version:8.x)有以下几种日志：

 - 错误日志      log_error  `show variables like "log_error"`
 - 普通查询日志   general_log
 - 慢查询日志     slow_query_log
 - 二进制日志     binlog

## 实战

mysql是使用brew安装的.

    [sujianhui@ xxx]$>sudo find / -name my.cnf
    /usr/local/etc/my.cnf
    /usr/local/Cellar/mysql/8.0.16/.bottle/etc/my.cnf

    [sujianhui@ saas-interact]$>cat /usr/local/etc/my.cnf 
    # Default Homebrew MySQL server config
    [mysqld]
    # Only allow connections from localhost
    bind-address = 127.0.0.1

追溯一下mysql服务是由哪个可执行文件启动的
    
    [sujianhui@ saas-interact]$>ps aux | grep mysql
    sujianhui          657   0.0  0.0  4892916   1568   ??  S    26Dec20   5:35.43 /usr/local/opt/mysql/bin/mysqld --basedir=/usr/local/opt/mysql --datadir=/usr/local/var/mysql --plugin-dir=/usr/local/opt/mysql/lib/plugin --log-error=bogon.err --pid-file=bogon.pid
    sujianhui          501   0.0  0.0  4296160      8   ??  S    26Dec20   0:00.03 /bin/sh /usr/local/opt/mysql/bin/mysqld_safe --datadir=/usr/local/var/mysql
    sujianhui        32108   0.0  0.0  4277256    804 s001  S+    1:52PM   0:00.00 grep --color=auto mysql
    
    [sujianhui@ saas-interact]$>pstree -p 501
    -+= 00001 root /sbin/launchd
     \-+= 00501 sujianhui /bin/sh /usr/local/opt/mysql/bin/mysqld_safe --datadir=/usr/local/var/mysql
       \--- 00657 sujianhui /usr/local/opt/mysql/bin/mysqld --basedir=/usr/local/opt/mysql --datadir=/usr/local/var/mysql --plugin-dir=/usr/local/opt/mysql/lib/plugin --log-error=bogon.err --pid-file=bogon.pid

    [sujianhui@ saas-interact]$>brew services mysqld stop
    Error: No available formula with the name "stop" 
    [sujianhui@ saas-interact]$>brew services stop mysql
    Stopping `mysql`... (might take a while)
    ==> Successfully stopped `mysql` (label: homebrew.mxcl.mysql)
    
可以看到可执行文件是一个软链接，原始服务由`/usr/local/Cellar/`目录下的mysql提供.该目录下是所有我们通过brew安装的软件.
 
    [sujianhui@ saas-interact]$>cd /usr/local/opt/
    ...
    [sujianhui@ opt]$>ll | grep mysql
    lrwxr-xr-x  1 sujianhui  admin    22B May 17  2019 mysql@ -> ../Cellar/mysql/8.0.16
    lrwxr-xr-x  1 sujianhui  admin    22B May 17  2019 mysql@8.0@ -> ../Cellar/mysql/8.0.16
    
    ... ... 
    [sujianhui@ mysql]$>cd Cellar/mysql/8.0.16/
    [sujianhui@ 8.0.16]$>ll
    total 1552
    -rw-r--r--   1 sujianhui  staff   1.1K May 17  2019 INSTALL_RECEIPT.json
    -rw-r--r--   1 sujianhui  staff   328K Apr 13  2019 LICENSE
    -rw-r--r--   1 sujianhui  staff   328K Apr 13  2019 LICENSE-test
    -rw-r--r--   1 sujianhui  staff    99K Apr 13  2019 LICENSE.router
    -rw-r--r--   1 sujianhui  staff   687B Apr 13  2019 README
    -rw-r--r--   1 sujianhui  staff   687B Apr 13  2019 README-test
    -rw-r--r--   1 sujianhui  staff   700B Apr 13  2019 README.router
    drwxr-xr-x  39 sujianhui  staff   1.2K May 17  2019 bin/
    -rw-r--r--   1 sujianhui  staff   543B May 17  2019 homebrew.mxcl.mysql.plist
    drwxr-xr-x   3 sujianhui  staff    96B Apr 13  2019 include/
    drwxr-xr-x  16 sujianhui  staff   512B Apr 13  2019 lib/
    drwxr-xr-x   6 sujianhui  staff   192B Apr 13  2019 share/
    drwxr-xr-x   5 sujianhui  staff   160B Jun 21  2019 support-files/


修改my.cnf,开启查询日志.

    [sujianhui@ etc]$>cat my.cnf 
    # Default Homebrew MySQL server config
    [mysqld]
    # Only allow connections from localhost
    bind-address = 127.0.0.1
    
    log-error='/usr/local/var/log/mysql/error.log'
    
    general_log = ON
    general_log_file = '/usr/local/var/log/mysql/mysql.log'
    
    long_query_time=2
    log-slow-queries='/usr/local/var/log/mysql/slowquery.log'

手动创建一下文件.
                      
    [sujianhui@ etc]$>cd ../var/log/
    [sujianhui@ log]$>ll
    total 1160
    drwxr-xr-x  4 sujianhui  admin   128B Apr  5  2019 nginx/
    -rw-r--r--  1 sujianhui  admin   564K Dec 30 19:40 php-fpm.log
    [sujianhui@ log]$>mkdir mysql
    [sujianhui@ log]$>ll
    total 1160
    drwxr-xr-x  2 sujianhui  admin    64B Jan  2 14:21 mysql/
    drwxr-xr-x  4 sujianhui  admin   128B Apr  5  2019 nginx/
    -rw-r--r--  1 sujianhui  admin   564K Dec 30 19:40 php-fpm.log

    [sujianhui@ mysql]$>touch mysql.log
    [sujianhui@ mysql]$>touch slowquery.log
    [sujianhui@ mysql]$>ll
    total 16
    -rw-r-----  1 sujianhui  admin   5.7K Jan  2 14:21 error.log
    -rw-r--r--  1 sujianhui  admin     0B Jan  2 14:21 mysql.log
    -rw-r--r--  1 sujianhui  admin     0B Jan  2 14:21 slowquery.log

    [sujianhui@ mysql]$>brew services restart mysql
    Stopping `mysql`... (might take a while)
    ==> Successfully stopped `mysql` (label: homebrew.mxcl.mysql)
    ==> Successfully started `mysql` (label: homebrew.mxcl.mysql)
    [sujianhui@ mysql]$>ps aux | grep mysql
    sujianhui        45314   0.0  0.0  4277256    820 s001  S+    2:22PM   0:00.00 grep --color=auto mysql
    
使用brew竟然不能启动mysql服务(没有仔细去看为什么,赶时间).手动启动一下.
    
    [sujianhui@ mysql]$>mysqld
    [sujianhui@ mysql]$>ps aux | grep mysql
    sujianhui        45594   0.1  4.7  4862060 393960   ??  S     2:22PM   0:01.70 /usr/local/opt/mysql/bin/mysqld --basedir=/usr/local/opt/mysql --datadir=/usr/local/var/mysql --plugin-dir=/usr/local/opt/mysql/lib/plugin --log-error=/usr/local/var/log/mysql/error.log --pid-file=bogon.pid
    sujianhui        45596   0.0  0.0  4267932    344 s001  R+    2:22PM   0:00.00 grep --color=auto mysql
    sujianhui        45460   0.0  0.0  4279776   1232   ??  S     2:22PM   0:00.03 /bin/sh /usr/local/opt/mysql/bin/mysqld_safe --datadir=/usr/local/var/mysql

## 总结


普通查询日志记录客户端连接信息和执行的sql语句信息

1.1 临时开启general_log日志开关

    mysql> show variables like 'general_log%';
    +------------------+---------------------------+
    | Variable_name    | Value                     |
    +------------------+---------------------------+
    | general_log      | OFF                       |            -> 默认为关闭状态
    | general_log_file | /data/3306/data/node1.log |
    +------------------+---------------------------+
    mysql> set global general_log = ON;
    mysql> set global general_log_file = "/data/3306/data/general_90root.log";
    mysql> show variables like 'general_log%';
    +------------------+------------------------------------+
    | Variable_name    | Value                              |
    +------------------+------------------------------------+
    | general_log      | ON                                 |
    | general_log_file | /data/3306/data/general_90root.log |
    +------------------+------------------------------------+
    
以上配置临时生效

1.2 永久开启general_log日志开关

    [root@node1 ~]# cat /data/3306/my.cnf
    [mysqld]
    character_set_server = utf8
    general_log = ON
    general_log_file = "/data/3306/data/general_90root.log";
      
    [root@node1 ~]# /data/3306/mysql restart
    [root@node1 ~]#  mysql -uroot -p90root3306 -S /data/3306/mysql.sock
    
    mysql> show variables like 'general_log%';
    +------------------+-------------------------------------+
    | Variable_name    | Value                               |
    +------------------+-------------------------------------+
    | general_log      | ON                                  |
    | general_log_file | /data/3306/data/general_90root.log  |
    +------------------+-------------------------------------+
    
    
生产环境一般是关闭的, 全量记录查询日志没有太大价值. 

2. 慢查询日志(slow query log)介绍

慢查询日志只记录执行时间超出指定值的sql语句. 慢查询的设置对于数据库sql的优化非常重要

    [root@node1 ~]# cat /data/3306/my.cnf
    [mysqld]
    long_query_time     = 1                         -> sql执行语句超过1秒记录到慢查询日志
    slow-query-log-file   = /data/3306/slow.log     -> 慢查询日志文件路径
    log_queries_not_using_indexes                   -> 没有走索引的sql语句记录到慢查询日志

## reference

https://blog.51cto.com/8649605/1855594