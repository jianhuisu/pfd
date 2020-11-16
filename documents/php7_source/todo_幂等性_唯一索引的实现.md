/**
     * 数据库中 room_id 没有创建唯一索引. 并发高的情况下没有办法实现 幂等下插入.
     * 原有系统是通过在 生成时加入时序及随机数来实现唯一索引. 调用时禁止并发调用. 但是如果使用相同参数对该接口进行压测.则会存在问题.
     * todo .
     * @param $params
     */
    public function createService($params)
    {
        $roomModel = new RoomModel();
        echo 2;exit;
        $res = $roomModel::create($params);
        echo \GuzzleHttp\json_encode($res);
        exit;
    }
