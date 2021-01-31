## Laravel/Lumen漂亮的解决主从库延迟问题 

laravel的数据配置文件.

    database:
        connections:
            default:
            driver: mysql
            host: rm-2ze8jb67f73y0b446.mysql.rds.aliyuncs.com
            read:
                database: test-vss
            write:
                database: test-vss
            username: test_operation
            password: Test_operation
            charset: utf8mb4
            collation: utf8mb4_unicode_ci
            prefix:
            strict: 0
            logger: true # 是否要写日志

`app/Providers/AppServiceProvider`增加boot方法，监听每条执行的SQL，如果发现`DML`语句（INSERT,UPDATE,DELETE）时，
则清空从库连接的pdo（readPdo），这样由于Laravel底层安全机制，会默认使用主库连接。这样没有使用事务时，
从根本上解决执行DML语句后，再执行`DQL`（SELECT）语句的延迟问题。

###  其它解决方案

直接查主其实并不保险.根据不同场景可以采用不同的解决方案

 - 异步通知
 - 从需求上避免
 - 写缓存