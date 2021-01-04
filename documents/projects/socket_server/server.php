<?php
//stream_server.php

$sockfile = '/Users/sujianhui/PhpstormProjects/pfd/sjh.sock';
// 如果sock文件已存在，先尝试删除
if (file_exists($sockfile))
{
    unlink($sockfile);
}

$server = stream_socket_server("unix://$sockfile", $errno, $errstr);

if (!$server)
{
    die("创建unix domain socket fail: $errno - $errstr");
}

while(1)
{
    $conn = stream_socket_accept($server, 600);

    if ($conn)
    {
        while(1)
        {
            $msg = fread($conn, 1024);
            if (strlen($msg) == 0) //客户端关闭
            {
                fclose($conn);
                break;
            }
            echo "read data: $msg";
            fwrite($conn, "read ok!");
        }
    }

}
fclose($server);