<?php
/**
 * User: sujianhui
 * Date: 2018/10/22
 * Time: 18:16
 */
namespace app\controller;

use vendor\base\Event;
use vendor\db\db_mysqli;

class SiteController extends BaseController
{
    public function actionIndex()
    {

        $conn = mysqli_connect("127.0.0.1","root","MyName@2991","qq");

        if (!$conn) {
            echo "连接失败！";
            echo mysqli_connect_error();
            exit();
        }


        $conn1 = mysqli_connect("127.0.0.1","root","MyName@2991","qq");

        if (!$conn1) {
            echo "连接失败！";
            echo mysqli_connect_error();
            exit();
        }

        $conn2 = mysqli_connect("127.0.0.1","root","MyName@2991","qq");

        if (!$conn2) {
            echo "连接失败！";
            echo mysqli_connect_error();
            exit();
        }

        mysqli_close($conn2);

//        while(1){
//
//        }


    }

}