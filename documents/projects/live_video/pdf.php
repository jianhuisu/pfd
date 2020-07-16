<?php
function main() {
    // 如果将代码放在 web 服务中则可能需要用到以下两行
    // 可在 php.ini 修改具体的值
    ini_set('max_execution_time', '0'); // 不限制处理时间
    ini_set('memory_limit', '-1'); // 取消内存大小限制

    $imgs = pdf2jpg('./new.pdf', '.');

    $all_size = pic_max_size($imgs); // 返回底图的宽和高

    $base_pic = create_base_pic($all_size[0], $all_size[1], '.'); // 生成底图

    $bigimage = create_bigimage($imgs, $base_pic, '.'); // 拼接成长图

    // 清理转化过程的中间文件
    foreach ($imgs as $val) {
        unlink($val);
    }
    unlink($base_pic);
}

// $imgs array 小图数组, $target string 长图底图
function create_bigimage($imgs, $target, $dirpath) {
    $target_img = Imagecreatefromjpeg($target);

    $source = array();
    foreach ($imgs as $k => $v) {
        $source[$k]['source'] = Imagecreatefromjpeg($v);
        $source[$k]['size'] = getimagesize($v);
    }

    $tmpx = 0;
    $tmpy = 0; //图片之间的间距
    for ($i = 0; $i < count($imgs); $i++) {
        imagecopy($target_img, $source[$i]['source'], $tmpx, $tmpy, 0, 0, $source[$i]['size'][0], $source[$i]['size'][1]);
        $tmpy = $tmpy + $source[$i]['size'][1];
    }
    $filename = $dirpath . '/' . substr(md5(uniqid(rand())), 0, 15) . '.jpg';
    Imagejpeg($target_img, $filename);
    imagedestroy($target_img);
    return $filename;
}

function create_base_pic($width, $height, $dirpath) {
    $filename = substr(md5(uniqid(rand())), 0, 15). '.jpg';
    $filepath = $dirpath . $filename;
    if (!is_dir($dirpath)) {
        mkdir($dirpath);
    };
    $im =imagecreate($width, $height);
    ImageColorAllocate ($im, 25, 255, 255);
    imagejpeg($im, $filepath, 0);
    imagedestroy($im);
    return $filepath;
}

function pic_max_size($arr) {
    list($width, $height, $type, $attr) = getimagesize($arr[0]);
    $height = $height * count($arr);

    return [$width, $height];
}

// 将 PDF 转成图片
function pdf2jpg($pdf, $dirpath) {
    if (!extension_loaded('imagick')) {
        return false;
    }
    if (!file_exists($pdf)) {
        return false;
    }
    $im = new imagick();
    $im->setResolution(320, 320);

    $im->setCompressionQuality(100);
    $im->readImage($pdf);
    foreach ($im as $key => $var) {
        $var->setImageFormat('jpg');
        $filename = $dirpath . '/' . md5($key.time()) . '.jpg';
        if ($var->writeImage($filename) == true) {
            $return[] = $filename;
        }
    }
    return $return;
}

main();
