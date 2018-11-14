<?php
/**
 * User: sujianhui
 * Date: 2018/11/8
 * Time: 10:55
 */
namespace app\model;


use vendor\db\db_mysqli;

class MessageModel
{
    public static function tableName()
    {
        return "message";
    }

    public static function send($send,$to,$message)
    {
        $db = db_mysqli::getInstance();
        $sql = "insert into ".self::tableName()."(send_id,receive_id,content,send_time) values({$send},{$to},'{$message}',".time().")";
        $db->execute($sql);
    }

    public static function generateMessage()
    {
        return "hello Mr".date("Y-m-d H:i:s");
    }


}