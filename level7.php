<?php
/**
 * Created by PhpStorm.
 * User: sujianhui
 * Date: 2019-05-29
 * Time: 13:21
 */
$timeList = range(strtotime('2019-05-01'), strtotime('-1 days',strtotime('2019-05-04')), 3600 * 24);

$columns = array_map(function ($t){
    $date = date('Y-m-d',$t);
    return ['st' => $date, 'ed' => $date,'title' => $date];
},$timeList);

var_dump($columns);exit;





