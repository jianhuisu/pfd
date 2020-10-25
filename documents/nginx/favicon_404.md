# favicon.ico 404 Not found.

`favicon.ico` 文件是浏览器收藏网址时显示的图标，当第一次访问页面时，浏览器会自动发起请求获取页面的favicon.ico文件。
当/favicon.ico文件不存在时，服务器会记录`404`日志。这样有两个缺点：

 1. 使`access.log`文件变大，记录很多没有用的数据。
 2. 因为大部分是`favicon.ico 404`信息，当要查看信息时，会影响搜寻效率.


解决方法如下：在nginx的配置中加入

    location = /favicon.ico {
      log_not_found off;
      access_log off;
    }
    
以上配置说明：

 - `log_not_found off` 关闭日志
 - `access_log off` 不记录在`access.log`