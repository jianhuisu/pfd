<?php
namespace app\controller;

use app\model\MessageModel;

class MessageController extends BaseController
{
    public function actionSend()
    {
        $sendId     = 2;
        $receiveId  = 1;
        $message    = MessageModel::generateMessage();

        MessageModel::send($sendId,$receiveId,$message);
        exit(1);

    }

    public function actionList()
    {

    }

}