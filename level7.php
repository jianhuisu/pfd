<?php

$pattern = '/http\:\/\/www\.baidu\.com\/module\/(\w+)\/(\w+)(?<=\?)(\S+)/';
$str     = "http://www.baidu.com/module/controller/action?name=su&type=1";
$matchResult = [];

preg_match_all($pattern,$str,$matchResult);


if($matchResult){
	print_r($matchResult);
	var_dump($matchResult);
} else {
	echo "not match";
}

