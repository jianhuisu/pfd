## mysql8.0

### install 

    wget https://dev.mysql.com/get/mysql80-community-release-el7-1.noarch.rpm
    yum localinstall mysql80-community-release-el7-1.noarch.rpm
    yum repolist enabled | grep "mysql.*-community.*"
    vim /etc/yum.repos.d/mysql-community.repo   // set default version
    yum install mysql-community-server
    systemctl start mysqld
    systemctl enable mysqld
    systemctl daemon-reload
    
### user set 

#### 找到安装后的临时密码

    [guangsu@xuwei bin]$ sudo grep 'temporary password' /var/log/mysqld.log
    [guangsu@xuwei bin]$ mysql -uroot -p
    Enter password: 

    Welcome to the MySQL monitor.  Commands end with ; or \g.
    Your MySQL connection id is 8
    Server version: 8.0.19
    ...

更改root的密码,注意`'root'@'localhost'` and `'root'@'%'`实际上是两个用户.     
 
    mysql> ALTER USER 'root'@'localhost' IDENTIFIED BY 'Debugger123@xuwei';
    Query OK, 0 rows affected (0.01 sec)

或者我们可以使用 `set password for 'root'@'localhost'=password('TestBicon@123');`(but not recommend)

授权root可以在任意远程IP登陆. 

    mysql> GRANT ALL ON *.* TO 'root'@'%';

添加另外一个用于运营的`msyql user`  
        
    mysql> use mysql;
    Reading table information for completion of table and column names
    You can turn off this feature to get a quicker startup with -A
    
    Database changed
    mysql> create user 'guangsu'@'%' identified by '4466xdebug_User';
    Query OK, 0 rows affected (0.02 sec)
    
    mysql> ALTER USER 'guangsu'@'%' IDENTIFIED WITH mysql_native_password BY '4466xdebug_User';
    Query OK, 0 rows affected (0.02 sec)
    
    mysql> grant all privileges on *.* to guangsu;
    Query OK, 0 rows affected (0.02 sec)
    
    mysql> show grants for guangsu\G
    *************************** 1. row ***************************
    Grants for guangsu@%: GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP,  ON *.* TO `guangsu`@`%`
    *************************** 2. row ***************************
    Grants for guangsu@%: GRANT ...,XA_RECOVER_ADMIN ON *.* TO `guangsu`@`%`
    2 rows in set (0.00 sec)
    
    mysql> flush privileges;
    Query OK, 0 rows affected (0.01 sec)

#### 设置数据库使用`utf8mb4`字符集

    mysql> SHOW VARIABLES WHERE Variable_name LIKE 'character_set_%' OR Variable_name LIKE 'collation%';
    +--------------------------+--------------------------------+
    | Variable_name            | Value                          |
    +--------------------------+--------------------------------+
    | character_set_client     | utf8mb4                        |
    | character_set_connection | utf8mb4                        |
    | character_set_database   | utf8mb4                        |
    | character_set_filesystem | binary                         |
    | character_set_results    | utf8mb4                        |
    | character_set_server     | utf8mb4                        |
    | character_set_system     | utf8                           |
    | character_sets_dir       | /usr/share/mysql-8.0/charsets/ |
    | collation_connection     | utf8mb4_0900_ai_ci             |
    | collation_database       | utf8mb4_0900_ai_ci             |
    | collation_server         | utf8mb4_0900_ai_ci             |
    +--------------------------+--------------------------------+
    11 rows in set (0.00 sec)

不需要修改

#### 如何卸载刚才安装mysql-server 

    yum remove mysql-community-server
    
find use cmd like `rpm -qa | grep mysql` and use `yum remove name` to remove , until all remove (`rpm -qa | grep -i mysql` => `rpm -e name`) 

删除生成的数据文件，日志文件,配置文件 

    rm -rf /var/lib/mysql
    rm /etc/my.cnf
    rm -rf /usr/share/mysql-8.0 

#### 参考资料 

https://blog.csdn.net/qq_38591756/article/details/82958333