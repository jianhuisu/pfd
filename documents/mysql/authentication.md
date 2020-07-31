## 客户端身份认证

Q.1  mysql server升级到8.x后,客户端连接问题

    PHP Warning:  mysqli_connect(): The server requested authentication method unknown to the client [caching_sha2_password] in
    
`php mysqli`扩展不支持新的`caching_sha2`身份验证功能，得等到他们发布新版本.我在`DataGrip`中下载了`jdbc driver`可以支持最新的`caching_sha2`认证方式.

解决办法

 - 修改已有用户 `ALTER USER 'guangsu'@'%' IDENTIFIED WITH mysql_native_password BY '4466xdebug_User';`
 - 新建登录用户 `CREATE USER 'guangsu'@'%' IDENTIFIED WITH mysql_native_password BY '4466xdebug_User';`