## STDIN STDOUT

`php://stdin`、`php://stdout`、`php://stderr`

usage example :

    <?php
    echo "请输入内容:";
    $jimmy = fgets(STDIN);
    echo sprintf("输入的内容为: %s\n", $jimmy);
    $demo = fopen('php://stdin', 'r');
    echo "请输入: ";
    $test = fread($demo, 12); //最多读取12个字符
    echo sprintf("输入为: %s\n", $test);fclose($demo);