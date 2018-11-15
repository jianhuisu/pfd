<?php
/**
 * User: sujianhui
 * Date: 2017-12-4
 * Time: 15:32
 */
include './db.php';

header('content-type:text/html;charset=utf-8');
?>

<html>
<style>
    .po{
        border:1px solid black;
        width:420px;
        height:600px;
    }
    .po .frame{
        margin: 2px auto;
    }
    .list{
        width:98%;
        height:61%;
    }
    .send{
        width:98%;
        height:30%;
    }
    #send{
        height:30px;
        width:60px;
        background-color: aqua;
        color:white;
        float:right;
        border-radius: 3px;
        text-align: center;
        line-height:30px;
        cursor:pointer;
    }
    #send:hover{
        background-color: blue;
    }
    .frame{
        border:1px solid black;
    }
</style>
<h4>美研本科名师</h4>
<div class="po">
    <div class="list frame"></div>
    <div class="send frame">
            <textarea name="send_body" id="" cols="56" rows="10">

            </textarea>
    </div>
    <div id="send">发送</div>
</div>
</html>

