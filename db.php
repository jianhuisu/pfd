<?php
/**
 * User: sujianhui
 * Date: 2017-12-4
 * Time: 14:35
 */

$db = new mysqli('localhost', 'root', '', 'test');

//$query = "SELECT * FROM abc";
//
//$result = $db->query($query);
//
//$result_num = $result->num_rows;
//
//$row = $result->fetch_assoc();  //返回一个关联数组，可以通过$row['uid']的方式取得值
//$row = $result->fetch_row();  //返回一个索引数组，可以通过$row[0]的方式取得值
//$row = $result->fetch_array();  //返回一个混合数组，可以通过$row['uid']和$row[0]两种方式取得值
//$row = $result->fetch_object();  //返回一个对象，可以通过$row->uid的方式取得值
//
////释放结果集
//$result->free();

//关闭一个数据库连接，这不是必要的，因为脚本执行完毕时会自动关闭连接
//$db->close();

