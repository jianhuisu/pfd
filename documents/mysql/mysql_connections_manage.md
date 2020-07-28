## mysql的链接管理

静态查看:

 - `SHOW PROCESSLIST`/`SHOW FULL PROCESSLIST`;  查询当前用户/全局的连接情况
 - `SHOW VARIABLES LIKE '%max_connections%'`;   查询配置文件中的配置的最大并发连接数
 - `SHOW STATUS LIKE '%Threads_connected%'`;    查询当前的并发连接数        

注意：

 - VARIABLE 更倾向于表示我们在`my.cnf`里面预定义的配置选项,查看命令格式类似于`mysql> show variables where variable_name like 'character_set_%' or variable_name like 'set_%';` 
 - STATUS   更倾向于表示`mysql-server`在实际运行过程中的一些运行指标

实时查看：

    mysql> show status like 'Threads%';  
    +-------------------+-------+  
    | Variable_name     | Value |  
    +-------------------+-------+  
    | Threads_cached    | 58    |  
    | Threads_connected | 57    |   ### 这个数值指的是当前的并发连接数  
    | Threads_created   | 3676  |  
    | Threads_running   | 4     |   ###这个数值指的是激活的连接数，这个数值一般远低于connected数值  并且一般大于>=2 （current_login_session+daemon）
    +-------------------+-------+  
       
`Threads_connected`跟`show processlist`结果相同，表示当前连接数。准确的来说，`Threads_running`是代表当前并发连接数.  
       
这是是查询数据库当前设置的最大连接数
  
    mysql> show variables like '%max_connections%';  
    +-----------------+-------+  
    | Variable_name   | Value |  
    +-----------------+-------+  
    | max_connections | 100  |  
    +-----------------+-------+  
       
可以在/etc/my.cnf里面设置数据库的最大连接数
  
    max_connections = 1000  

#### eg.1 同时建立并维持多个mysql连接.

    <?php
    
    $conn = [];
    
    for($i=0;$i<10;$i++){
    
        // 这样每一个链接都会占用一个端口
        $conn[] = mysqli_connect("127.0.0.1","sujianhui","xdebug_XDEBUG_5566","mysql");
        echo "success\n";
        
    }
    
    // 延长脚本的执行时间,防止脚本运行结束 导致连接释放
    sleep(1000);
    
mysql控制台中监测

    mysql> show full processlist;
    +-----+-----------------+------------------+-------+---------+------+------------------------+-----------------------+
    | Id  | User            | Host             | db    | Command | Time | State                  | Info                  |
    +-----+-----------------+------------------+-------+---------+------+------------------------+-----------------------+
    |   5 | event_scheduler | localhost        | NULL  | Daemon  | 1207 | Waiting on empty queue | NULL                  |
    |   8 | root            | localhost        | mysql | Query   |    0 | starting               | show full processlist |
    | 122 | sujianhui       | 172.17.0.1:51232 | mysql | Sleep   |    3 |                        | NULL                  |
    | 123 | sujianhui       | 172.17.0.1:51236 | mysql | Sleep   |    3 |                        | NULL                  |
    | 124 | sujianhui       | 172.17.0.1:51240 | mysql | Sleep   |    3 |                        | NULL                  |
    | 125 | sujianhui       | 172.17.0.1:51244 | mysql | Sleep   |    3 |                        | NULL                  |
    | 126 | sujianhui       | 172.17.0.1:51248 | mysql | Sleep   |    3 |                        | NULL                  |
    | 127 | sujianhui       | 172.17.0.1:51252 | mysql | Sleep   |    3 |                        | NULL                  |
    | 128 | sujianhui       | 172.17.0.1:51256 | mysql | Sleep   |    3 |                        | NULL                  |
    | 129 | sujianhui       | 172.17.0.1:51260 | mysql | Sleep   |    3 |                        | NULL                  |
    | 130 | sujianhui       | 172.17.0.1:51264 | mysql | Sleep   |    3 |                        | NULL                  |
    | 131 | sujianhui       | 172.17.0.1:51268 | mysql | Sleep   |    3 |                        | NULL                  |
    +-----+-----------------+------------------+-------+---------+------+------------------------+-----------------------+
    12 rows in set (0.00 sec)
    
#### 

    <?php
    
    $conn = [];
    
    for($i=0;$i<10;$i++){
    
        // 这样每一个链接都会占用一个端口
        $conn = mysqli_connect("127.0.0.1","sujianhui","xdebug_XDEBUG_5566","mysql");
        echo "success\n";
    
    }
    
    // 延长脚本的执行时间,防止脚本运行结束 导致连接释放
    sleep(1000);

存储连接资源的变量被覆盖时,也就是对该资源的引用计数为`0`时,连接自动释放,mysql控制台中只会保持最后一次连接.即使你没有手动调用`mysqli_close($conn)`释放.

    mysql> show full processlist;
    +-----+-----------------+------------------+-------+---------+------+------------------------+-----------------------+
    | Id  | User            | Host             | db    | Command | Time | State                  | Info                  |
    +-----+-----------------+------------------+-------+---------+------+------------------------+-----------------------+
    |   5 | event_scheduler | localhost        | NULL  | Daemon  | 1514 | Waiting on empty queue | NULL                  |
    |   8 | root            | localhost        | mysql | Query   |    0 | starting               | show full processlist |
    | 141 | sujianhui       | 172.17.0.1:51326 | mysql | Sleep   |    3 |                        | NULL                  |
    +-----+-----------------+------------------+-------+---------+------+------------------------+-----------------------+
    3 rows in set (0.00 sec)
    
    