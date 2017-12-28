<?php
/**
 * User: sujianhui
 * Date: 2017-12-23
 * Time: 17:41
 */
$host = '192.168.32.10';
$port = '19910';

//创建服务端的socket套接流,net协议为IPv4，protocol协议为TCP
$socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);

/*

    socket服务在服务端运行类似于mysql中数据的关系

    socket 抽象层处于应用进程  与 传输层之间 ,是应用 与 TCP 协议的 接口

    socket_bind 绑定接收的套接流主机和端口,与客户端相对应
    socket_listen

    这里的意思应该是  刚刚创建的socket服务
    将此 php 进程服务 （假设PID 为 5388） 与 severHost(192.168.32.10)、port(19910) 绑定
    当 severHost端socket 的 监听到 port (19910) 有请求时,转交由 php进程 5388 处理

    客户端不需要知道服务端(php)脚本(socket/websocket.php)处理业务逻辑,
    只需要知道 severHost 的 IP 地址 + 通讯端口，就可以建立连接

*/
if(socket_bind($socket,$host,$port) == false){
    echo 'server bind fail:'.socket_strerror(socket_last_error());
    /*这里的127.0.0.1是在本地主机测试，你如果有多台电脑，可以写IP地址*/
}

//监听套接流
if(socket_listen($socket,4)==false){
    echo 'server listen fail:'.socket_strerror(socket_last_error());
}

//让服务器无限获取客户端传过来的信息
do{

    /*接收客户端传过来的信息*/
    /*socket_accept的作用就是接受socket_bind()所绑定的主机发过来的套接流*/
    $accept_resource = socket_accept($socket);

    if($accept_resource !== false){

        socket_select($changed, $null, $null, 0, 10);

        /*读取客户端传过来的资源，并转化为字符串*/
        $string = socket_read($accept_resource,1024);
        /*socket_read的作用就是读出socket_accept()的资源并把它转化为字符串*/


        if($string != false){

            echo "one connect from client\r\n";

            getResponseHeader($string,$accept_resource,$host,$port);
            /*向socket_accept的套接流写入信息，也就是回馈信息给socket_bind()所绑定的主机客户端*/

            /*socket_write的作用是向socket_create的套接流写入信息，或者向socket_accept的套接流写入信息*/
            // socket_recv 阻塞其它请求
            while(socket_recv($accept_resource, $buf, 1024, 0) >= 1)
            {
                if(empty($j)){
                    $j = 1;
                }

                $receiveMessage = unmask($buf);
                echo $receiveMessage."\r\n";

                $sendMessage = mask($receiveMessage);
                $sent = socket_write($accept_resource, $sendMessage, strlen($sendMessage));
                echo '__j__'.$j++."\r\n";
            }


        }else{
            echo 'socket_read is fail';
        }

        /*socket_close的作用是关闭socket_create()或者socket_accept()所建立的套接流*/
        //socket_close($accept_resource);
    }

}while(true);

socket_close($socket);

//编码数据
function mask($text)
{
    $b1 = 0x80 | (0x1 & 0x0f);
    $length = strlen($text);

    if($length <= 125){
        $header = pack('CC', $b1, $length);
    } elseif($length > 125 && $length < 65536){
        $header = pack('CCn', $b1, 126, $length);
    } elseif($length >= 65536){
        $header = pack('CCNN', $b1, 127, $length);
    }

    return $header.$text;
}

//解码数据
function unmask($text) {
    $length = ord($text[1]) & 127;
    if($length == 126) {
        $masks = substr($text, 4, 4);
        $data = substr($text, 8);
    }
    elseif($length == 127) {
        $masks = substr($text, 10, 4);
        $data = substr($text, 14);
    }
    else {
        $masks = substr($text, 2, 4);
        $data = substr($text, 6);
    }
    $text = "";
    for ($i = 0; $i < strlen($data); ++$i) {
        $text .= $data[$i] ^ $masks[$i%4];
    }
    return $text;
}

//function getResponseHeader($header,$tmpSocket,$host,$port){
//
//    //  PHP_EOL 是换行 不同于 CRLF 所以这种获取是有问题的
//    $head = explode(PHP_EOL,$header);
//
//    $SecWebSocketAccept = '';
//    foreach($head as $v ){
//
//        if(strpos($v,':') != false){
//            list($name,$value)  = explode(':',$v);
//            if($name == 'Sec-WebSocket-Key'){
//                $SecWebSocketAccept = base64_encode(pack('H*',sha1($value.'258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
//                break;
//            }
//        }
//
//    }
//
//    // 响应头中 要使用 $host  取代 $_SERVER['REMOTE_ADDR'] 有的时候该预定义变量无法获取到
//    $response  = "HTTP/1.1 101 Switching Protocols\r\n" .
//        "Upgrade: websocket\r\n" .
//        "Connection: Upgrade\r\n" .
//        "WebSocket-Origin: ".$host."\r\n" .
//        "Sec-WebSocket-Accept:$SecWebSocketAccept\r\n\r\n";
//        // 对于HTTP请求格式来说，头部和主体内容之间有一个回车换行符(CRLF)是相当重要的。
//
//    socket_write($tmpSocket,$response,strlen($response));
//}

//握手的逻辑
function getResponseHeader($receved_header,$client_conn, $host, $port)
{
    $headers = array();
    $lines = preg_split("/\r\n/", $receved_header);
    foreach($lines as $line)
    {
        $line = chop($line);
        if(preg_match('/\A(\S+): (.*)\z/', $line, $matches))
        {
            $headers[$matches[1]] = $matches[2];
        }
    }

    $secKey = $headers['Sec-WebSocket-Key'];
    $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
    $upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
        "Upgrade: websocket\r\n" .
        "Connection: Upgrade\r\n" .
        "WebSocket-Origin: $host\r\n" .
        "WebSocket-Location: ws://$host:$port/demo/shout.php\r\n".
        "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
    socket_write($client_conn,$upgrade,strlen($upgrade));
}