<?php

$conn = mysqli_connect("127.0.0.1","guangsu","4466xdebug_User","yii2advanced");

if (!$conn) {
    echo "连接失败！";
    echo mysqli_connect_error();
    exit();
}

mysqli_query($conn,"set names utf8");

$sql = "select count(1) from test_1";

$result = mysqli_query($conn,$sql);

if($result === false) {

    printf("errorMsg: %s\n", mysqli_error($conn));
    throw new \mysqli_sql_exception(mysqli_error($conn));
    exit;
}

$fetchRes = [];
// mysqli_fetch_assoc($result) 关联
// mysqli_fetch_row($result)  索引
// mysqli_fetch_array($result)  assoc + row
while($row = mysqli_fetch_assoc($result))
{
    $fetchRes[] = $row;
}

var_dump($fetchRes);

while(1){};