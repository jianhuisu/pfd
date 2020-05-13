<?php

$conn = mysqli_connect("127.0.0.1","root","123456","im");

if (!$conn) {
    echo "连接失败！";
    echo mysqli_connect_error();
    exit();
}

echo "success\n";