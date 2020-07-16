<?php

$conn = [];

for($i=0;$i<10;$i++){

    // 这样每一个链接都会占用一个端口
    $conn[] = mysqli_connect("127.0.0.1","sujianhui","xdebug_XDEBUG_5566","mysql");
    echo "success\n";

}

// 不释放链接
sleep(1000);