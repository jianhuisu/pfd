## php extension

PHP 的扩展库有两种编译形式

 - `static`  编译PHP时，指定参数，将扩展静态化编译到`PHP二进制文件`中
 - `dynamic` PHP编译完成后，利用`phpize`+`./configure`编译扩展的动态库，供PHP启动时加载。
 
`dynamic load` need modify `php.ini`
    
    php --ini
    vim php.ini
    extension=path/helloworld.so
    :wq
    php -m
 
前一种启动时一次性`load`,后一种方式比较灵活，安装以后还可以随时追加扩展库进去.

    if(!extension_loaded('helloworld')) {
    	dl('helloworld.' . PHP_SHLIB_SUFFIX);
    }
    
    if(extension_loaded("helloworld")){
        $module = 'helloworld';
        $functions = get_extension_funcs($module);
    
        foreach($functions as $e){
            echo "function Name is : ".$e."\n";
            echo "function return Value is : ".$e(" - self input - ")."\n";;
        }
    
    } else {
        echo "load extension fail \n";
    }
    
##### develop one php extension

    cd /php_source/php-7.1.0/ext
    ./ext_skel --extname=helloworld
    vim config.m4 && vim helloworld.c
    /home/sujianhui/CLionProjects/output/php7_1/bin/phpize
    ./configure --with-php-config=/home/sujianhui/CLionProjects/output/php7_1/bin/php-config
    make && make install
    vim php.ini 
    php helloworld.php

##### install one extension

    /home/sujianhui/CLionProjects/output/php7_1/bin/phpize
    ./configure --with-php-config=/home/sujianhui/CLionProjects/output/php7_1/bin/php-config
    make && make install
    vim php.ini
    
or 

    pecl install swoole
