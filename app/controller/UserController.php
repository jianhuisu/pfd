<?php
/**
 * User: sujianhui
 * Date: 2018/11/7
 * Time: 15:08
 */
namespace app\controller;

use vendor\db\db_mysqli;

class UserController extends BaseController
{
    public function actionFriendlist()
    {
        $db = db_mysqli::getInstance();

        // get friends list
        $sql = 'select f.fid,u.name from friends f inner join user u on f.fid=u.id where f.uid=1 ';

        // query user having friend by fid select count(1) from friends where uid=1 and fid=?
        $result = $db->query($sql);
    }

    public function actionCreate()
    {
        $db = db_mysqli::getInstance();
        $sql = "insert into user(name) values('sujianhui_1'),('sujianhui_2'),('sujianhui_3'),('sujianhui_4')";
        $db->execute($sql);

    }


}