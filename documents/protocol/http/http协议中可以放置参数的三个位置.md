# HTTP请求中放置参数的三个位置

一个http请求可以添加参数的地方有三个

 - URL get 方式添加的参数存储在URL中
 - BODY post 方式所添加的 params是存储在 body 体中
 - HEADER header 中添加的参数 放置在 请求头中. 通过 HTTP_NAME  => value 的方式获取

#### POST 

HTTP/1.1 协议规定的 HTTP 请求方法有 OPTIONS、GET、HEAD、POST、PUT、DELETE、TRACE、CONNECT 这几种。其中 POST 一般用来向服务端提交数据，主要讨论 POST 提交数据的几种方式。
协议规定 POST 提交的数据必须放在消息主体（entity-body）中，但协议并没有规定数据必须使用什么编码方式。实际上，开发者完全可以自己决定消息主体的格式，只要最后发送的请求满足HTTP请求的格式就可以。

 - application/x-www-form-urlencoded
 - multipart/form-data
 - application/json
 - text/xml

#### HEADER

一些比较复杂或者安全性要求较高的服务都会遇到要在请求头中自定义一些头信息.

