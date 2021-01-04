<?php

$portlist = stream_get_transports();

$client = stream_socket_client("unix:///Users/sujianhui/PhpstormProjects/pfd/sjh.sock", $errno, $errstr);

if (!$client)
{
    die("connect to server fail: $errno - $errstr");
}

while(1)
{
    $msg = fread(STDIN, 1024);

    if ($msg == "quit\n")
    {
        break;
    }

    fwrite($client, $msg);
    $rt = fread($client, 1024);

    echo $rt . "\n";
}

fclose($client);