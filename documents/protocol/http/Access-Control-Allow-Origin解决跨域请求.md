# 利用Access-Control-Allow-Origin响应头解决跨域请求原理

传统的跨域请求没有好的解决方案，无非就是jsonp和iframe，随着跨域请求的应用越来越多，W3C提供了跨域请求的标准方案（Cross-Origin Resource Sharing）.
IE8、Firefox 3.5 及其以后的版本、Chrome浏览器、Safari 4 等已经实现了 Cross-Origin Resource Sharing 规范，实现了跨域请求。

在服务器响应客户端的时候，带上`Access-Control-Allow-Origin`头信息.

 - `Access-Control-Allow-Origin:*`，则允许所有域名的脚本访问该资源。
 - `Access-Control-Allow-Origin:http://www.phpddt.com.com`,允许特定的域名访问

如PHP添加响应头信息：

    <?php
    header("Access-Control-Allow-Origin: *");