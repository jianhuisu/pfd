<?php

header('Content-Type: text/event-stream'); // 以事件流的形式告知浏览器进行显示
header('Cache-Control: no-cache');         // 告知浏览器不进行缓存
header('X-Accel-Buffering: no');           // 关闭加速缓冲

$a = ob_get_level();
if (ob_get_level() == 0){
    ob_start();
}else{
    // buffer is opened
}

// 关闭隐式调用绝对刷新 防止默认设置影响正常逻辑
ob_implicit_flush(false);

for ($i = 0; $i<5; $i++){

    echo str_pad(' ',2048,' ');
    echo "Line to show.\n{$i}<br />";

    ob_flush();    //将 php 缓冲区的数据输出到 nginx
    flush();       //将 nginx 缓冲区的数据发送到浏览器
    sleep(2);
}

//关闭并清理缓冲区
ob_end_flush();



/*
10:53:40.258111 IP localhost.45133 > localhost.cslistener: Flags [S], seq 1263577883, win 32792, options [mss 16396,sackOK,TS val 624937 ecr 0,nop,wscale 6], length 0
10:53:40.258122 IP localhost.cslistener > localhost.45133: Flags [S.], seq 1993571885, ack 1263577884, win 32768, options [mss 16396,sackOK,TS val 624937 ecr 624937,nop,wscale 6], length 0
10:53:40.258134 IP localhost.45133 > localhost.cslistener: Flags [.], ack 1, win 513, options [nop,nop,TS val 624937 ecr 624937], length 0

10:53:40.258535 IP localhost.45133 > localhost.cslistener: Flags [P.], seq 1:897, ack 1, win 513, options [nop,nop,TS val 624937 ecr 624937], length 896
10:53:40.258544 IP localhost.cslistener > localhost.45133: Flags [.], ack 897, win 540, options [nop,nop,TS val 624937 ecr 624937], length 0

10:53:40.267503 IP localhost.cslistener > localhost.45133: Flags [P.], seq 1:257, ack 897, win 540, options [nop,nop,TS val 624946 ecr 624937], length 256
10:53:40.267516 IP localhost.45133 > localhost.cslistener: Flags [.], ack 257, win 530, options [nop,nop,TS val 624946 ecr 624946], length 0

10:53:42.268705 IP localhost.cslistener > localhost.45133: Flags [P.], seq 257:289, ack 897, win 540, options [nop,nop,TS val 626948 ecr 624946], length 32
10:53:42.268723 IP localhost.45133 > localhost.cslistener: Flags [.], ack 289, win 530, options [nop,nop,TS val 626948 ecr 626948], length 0

10:53:44.269817 IP localhost.cslistener > localhost.45133: Flags [P.], seq 289:321, ack 897, win 540, options [nop,nop,TS val 628949 ecr 626948], length 32
10:53:44.269859 IP localhost.45133 > localhost.cslistener: Flags [.], ack 321, win 530, options [nop,nop,TS val 628949 ecr 628949], length 0

10:53:46.270100 IP localhost.cslistener > localhost.45133: Flags [P.], seq 321:353, ack 897, win 540, options [nop,nop,TS val 630949 ecr 628949], length 32
10:53:46.270113 IP localhost.45133 > localhost.cslistener: Flags [.], ack 353, win 530, options [nop,nop,TS val 630949 ecr 630949], length 0

10:53:48.270367 IP localhost.cslistener > localhost.45133: Flags [P.], seq 353:385, ack 897, win 540, options [nop,nop,TS val 632949 ecr 630949], length 32
10:53:48.270380 IP localhost.45133 > localhost.cslistener: Flags [.], ack 385, win 530, options [nop,nop,TS val 632949 ecr 632949], length 0

10:53:50.271747 IP localhost.cslistener > localhost.45133: Flags [P.], seq 385:417, ack 897, win 540, options [nop,nop,TS val 634950 ecr 632949], length 32
10:53:50.271761 IP localhost.45133 > localhost.cslistener: Flags [.], ack 417, win 530, options [nop,nop,TS val 634950 ecr 634950], length 0

10:53:50.271779 IP localhost.cslistener > localhost.45133: Flags [F.], seq 417, ack 897, win 540, options [nop,nop,TS val 634950 ecr 634950], length 0
10:53:50.271884 IP localhost.45133 > localhost.cslistener: Flags [F.], seq 897, ack 418, win 530, options [nop,nop,TS val 634951 ecr 634950], length 0
10:53:50.272189 IP localhost.cslistener > localhost.45133: Flags [.], ack 898, win 540, options [nop,nop,TS val 634951 ecr 634951], length 0


  */