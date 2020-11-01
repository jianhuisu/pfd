<?php

$sourceFile = "1.tmp"; //要下载的临时文件名
$outFile = "用户订单.xls"; //下载保存到客户端的文件名
$file_extension = strtolower(substr(strrchr($sourceFile, "."), 1)); //获取文件扩展名
$len = filesize($sourceFile); //获取文件大小
$filename = basename($sourceFile); //获取文件名字
$outFile_extension = strtolower(substr(strrchr($outFile, "."), 1)); //获取文件扩展名

//根据扩展名 指出输出浏览器格式
switch ($outFile_extension) {
    case "exe" :
        $ctype = "application/octet-stream";
        break;
    case "zip" :
        $ctype = "application/zip";
        break;
    case "mp3" :
        $ctype = "audio/mpeg";
        break;
    case "mpg" :
        $ctype = "video/mpeg";
        break;
    case "avi" :
        $ctype = "video/x-msvideo";
        break;
    default :
        $ctype = "application/force-download";
}


header("Cache-Control:");
header("Cache-Control: public");
header("Content-Type: $ctype");
header("Content-Disposition: attachment; filename=" . $outFile);
header("Accept-Ranges: bytes");

$size = filesize($sourceFile);

if (isset ($_SERVER['HTTP_RANGE'])) {

   // $_SERVER['HTTP_RANGE'] : bytes=4390912-
    list ($a, $range) = explode("=", $_SERVER['HTTP_RANGE']);

    //if yes, download missing part
    // ??????
    str_replace($range, "-", $range);

    // form 0 increase
    $size2 = $size - 1; // range bytes offset
    $new_length = $size2 - $range; //获取下次下载的长度

    // if accept client breakpoint download request , should return 206 instead of 200 status code.
    header("HTTP/1.1 206 Partial Content");
    header("Content-Length: $new_length"); // 输入总长
    header("Content-Range: bytes $range$size2/$size"); // Content-Range: bytes has_download_bytes/filesize
} else {
    //第一次连接
    $size2 = $size - 1;
    header("Content-Range: bytes 0-$size2/$size"); //Content-Range: bytes 0-4988927/4988928
    header("Content-Length: " . $size); //输出总长
}

$fp = fopen("$sourceFile", "rb");
fseek($fp, $range);

set_time_limit(0);
while (!feof($fp)) {

    print (fread($fp, 1024 * 8)); //输出文件
    flush();
    ob_flush();

}

fclose($fp);
exit ();