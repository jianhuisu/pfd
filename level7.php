<?php

$a=[1,2,3];
foreach($a as &$v){
    //var_dump($v);
}
var_dump(key($a),current($a));
foreach($a as $v){
    //var_dump($v);
}

//current():取得目前指针位置的内容资料。
//key():读取目前指针所指向资料的索引值（键值）。

var_dump(key($a),current($a));

echo json_encode($a);