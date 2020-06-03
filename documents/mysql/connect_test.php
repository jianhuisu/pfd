<?php

for($i=0;$i<100;$i++){

    $conn = mysqli_connect("127.0.0.1","guangsu","Debugger123@xuwei","mysql");

    if (!$conn) {
        echo "连接失败！";
        echo mysqli_connect_error();
        exit();
    }
    sleep(5);
    echo "success\n";

}