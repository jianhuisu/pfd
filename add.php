<?php
/**
 * User: sujianhui
 * Date: 2017-12-23
 * Time: 15:03
 */
$x = array(
    1,
    2,
    3,
    4,
    5
);

foreach($x as $v){
    echo $v."\r\n";
    $s = 1;
    while($s){

        if($v>10){
            $s = 0;
            break;
        }
        $v++;
    }
}

echo 'finished';


while(socket_recv($accept_resource, $buf, 1024, 0) >= 1)
{
    if(empty($j)){
        $j = 1;
    }

    $receiveMessage = unmask($buf);
    echo $receiveMessage."\r\n";

    $sendMessage = mask($receiveMessage);
    $sent = socket_write($accept_resource, $sendMessage, strlen($sendMessage));
    echo '__j__'.$j++."\r\n";
}