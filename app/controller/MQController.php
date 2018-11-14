<?php
/**
 * User: sujianhui
 * Date: 2018/11/8
 * Time: 13:43
 */
namespace app\controller;

use vendor\db\db_mysqli;

class MQController extends BaseController
{
    public function actionPop()
    {

        set_time_limit(0); // 设置执行最长时间，0为无限制
        ignore_user_abort(true); // 关闭浏览器，服务器也能自动执行

        $db = db_mysqli::getInstance();

        while(1)
        {
            $sql = "select * from message where is_read=0 order by id asc limit 0,1";
            $result = $db->query($sql);

            if($result){

                $this->update($result[0]['id']);
                $this->push($result[0]);

            } else {
                echo date("Y-m-d H:i:s",time())." : mq is empty ...\n";
            }

            sleep(1);

        }

    }

    /**
     * 先更新 再发送 牺牲一定稳定性换取时间上的高效
     */
    public function push($struct)
    {

        $sendID = $struct['send_id'];
        $receiveID = $struct['receive_id'];
        $content = $struct['content'];

        // push message to receiver
        echo "UID : {$sendID} send message to UID :{$receiveID} : {$content}\n";
    }

    public function update($id)
    {
        $db = db_mysqli::getInstance();
        $sql = "update message set is_read=1 where id={$id}";
        $db->execute($sql);

    }


}