<?php
namespace vendor\db;

class db_mysqli
{
    protected static $db = null;

    private $link = null;

    private function __construct()
    {
        $this->connect();
    }

    public static function getInstance()
    {
        if(is_null(self::$db))
        {
            self::$db = new self();
        }

        return self::$db;
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public function connect()
    {
        $conn = mysqli_connect("127.0.0.1","root","123456","im");

        if (!$conn) {
        	echo "连接失败！";
        	echo mysqli_connect_error();
        	exit();
        }

        mysqli_query($conn,"set names utf8");

        $this->link = $conn;

    }

    public function query($sql)
    {

        //  $conn = $this->conn; 二者指向的一个地址还是 两个（写时复制）

        $conn = $this->link;

        // 指定编码格式
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

        return $fetchRes;
    }

    public function insert()
    {
        // todo mysql 写的时候 可以读吗
    }

    public function delete()
    {

    }

    public function update()
    {

    }

    /**
     * i	corresponding variable has type integer
       d	corresponding variable has type double
       s	corresponding variable has type string
       b	corresponding variable is a blob and will be sent in packets
     * @param $sql
     * @param string $name
     */
    public function execute($sql,$name = '')
    {

        $stmt = mysqli_prepare($this->link,$sql);
        //mysqli_stmt_bind_param($stmt,"s",$name);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

    }


}