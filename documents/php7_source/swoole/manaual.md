## 速查手册

### 事件列表

TCP 服务器

    onConnect
    onClose
    onReceive
    
UDP 服务器

    onPacket
    onReceive
    
HTTP 服务器

    onRequest
    
WebSocket 服务器

    onMessage
    onOpen
    onHandshake

### 函数

    WebSocket\Server::push

WebSocket 服务器向客户端发送数据应当使用  方法，此方法会进行 WebSocket 协议打包。长度最大不得超过2M
    
    Server::send
    
原始的 TCP 发送接口。不会按照WebSocket 协议打包。

    WebSocket\Server::disconnect()
    
从服务端主动关闭一个`WebSocket`连接，可以指定状态码.

    WebSocket\Server::isEstablished
    
判断是否为 websocket 连接

    WebSocket\Server::pack
    
打包 WebSocket 消息。返回打包好的 WebSocket 数据包，可通过 Swoole\Server 基类的 send () 发送给对端    