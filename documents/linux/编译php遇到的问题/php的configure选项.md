# configure的选项

原来都是从网上百度,粘贴过来直接执行. 将可以正确执行的命令记录到笔记里备用.但是没有细究选项的意思,今天碰到问题，吃了大亏.
在这时间珍贵的关头上，我死磕了一下午.

这个我原来在centos7上编译php7.3时正常使用的configure命令。

    ./configure --prefix=/usr/local/php
        --with-config-file-path=/usr/local/php
        --with-libdir=lib64   
        --with-curl 
        --with-freetype-dir 
        --with-gd 
        --with-gettext 
        --with-iconv-dir 
        --with-kerberos  
        --with-libxml-dir 
        --with-mysqli 
        --with-openssl  
        --with-pcre-regex 
        --with-pdo-mysql 
        --with-pdo-sqlite 
        --with-pear 
        --with-png-dir 
        --with-jpeg-dir 
        --with-xmlrpc 
        --with-xsl 
        --with-zlib 
        --with-bz2 
        --with-mhash 
        --enable-fpm 
        --enable-bcmath 
        --enable-libxml 
        --enable-inline-optimization 
        --enable-mbregex 
        --enable-mbstring 
        --enable-opcache 
        --enable-pcntl 
        --enable-shmop 
        --enable-soap 
        --enable-sockets 
        --enable-sysvsem 
        --enable-sysvshm 
        --enable-xml 
        --enable-zip
        
然后今天要在mac上编译一个高版本的php.用上边的命令出现了很多坑.

1 我编译的是php7.4，configure的选项有一些较大变动.一些选项格式改变,一些选项弃用.传送门 https://www.php.net/manual/zh/migration74.other-changes.php#migration74.other-changes.pkg-config
2 gettext与iconv冲突严重.
3 libiconv 死活寻找不到. 
4 各种库加载不上，设置了大量的临时变量 . 
5 超级大坑 `--with-libdir=lib64` 直接拿过来用，都没看一下.   

心得：

 1. 如果处于使用目的，还是使用稳定版本. 比如php7.4最新，那你还是用一下7.3. 这样可以少趟很多坑.
 1. 经常用的东西，吃饭的家伙,都看一下啊，别拿过来就用.


        
        