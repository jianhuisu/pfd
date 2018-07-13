<?php
/**
 * User: sujianhui
 * Date: 2017-10-25
 * Time: 10:25
 */
include './core.php';

$p = new \classes\decorator\Person();
$A = new \classes\decorator\ClothingA();
$B = new \classes\decorator\ClothingB();

$B->setPerson($p);
$A->setPerson($B);

$A->wear();


$url = 'http://www.shuqizw.com/book/5424/15610648.html';

$ht = new \classes\httpRequest();
$r = $ht->requestUseGet($url);


