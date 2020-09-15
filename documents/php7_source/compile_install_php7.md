## php7源码安装编译
 
    ./configure --prefix=/usr/local/php  
    --with-config-file-path=/usr/local/php 
    --with-curl 
    --with-freetype-dir 
    --with-gd 
    --with-gettext 
    --with-iconv-dir 
    --with-kerberos 
    --with-libdir=lib64 
    --with-libxml-dir 
    --with-mysqli 
    --with-openssl 
    -with-openssl-dir=/usr/include/openssl 
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
    --enable-xml --enable-zip

#### 配置 

我的环境没有安装以下依赖  

    #!/bin/bash
    
    cd ~/Downloads
    sudo yum -y install libxml2 libxml2-devel
    sudo yum -y install openssl openssl-devel
    sudo yum -y install bzip2 bzip2-devel -y
    sudo yum -y install curl curl-devel
    sudo yum -y install libjpeglibjpeg -devel
    sudo yum -y install libjpeg-devel
    sudo yum -y install libpng libpng-devel
    sudo yum -y install freetype-devel
    sudo yum -y install libxslt libxslt-devel
    
    yum  -y remove libzip-devel
    wget https://libzip.org/download/libzip-1.3.2.tar.gz
    tar xvf libzip-1.3.2.tar.gz
    cd libzip-1.3.2
    ./configure
    make && make install
    cd ~


添加到环境变量 

    vim /etc/profile
    export PATH=$PATH:/usr/local/php/bin

#### 如何反查configure参数

    php -i | head -n 10 

#### 启动php-fpm 

    [guangsu@xuwei local]$ php-fpm -D
    [24-Mar-2020 17:40:45] NOTICE: [pool www] 'user' directive is ignored when FPM is not running as root
    [24-Mar-2020 17:40:45] NOTICE: [pool www] 'group' directive is ignored when FPM is not running as root
    [guangsu@xuwei local]$ ps aux | grep php-fpm
    guangsu   5428  0.0  0.0 229048  6528 ?        Ss   17:40   0:00 php-fpm: master process (/usr/local/php/etc/php-fpm.conf)
    guangsu   5429  0.0  0.0 229048  5788 ?        S    17:40   0:00 php-fpm: pool www
    guangsu   5430  0.0  0.0 229048  5788 ?        S    17:40   0:00 php-fpm: pool www
    guangsu   5465  0.0  0.0 112712   964 pts/0    S+   17:41   0:00 grep --color=auto php-fpm

--------------------------------------------------------------------------------

#### 编译时错误解决：

共享库存在但是就是无法找到.`/ete/ld.conf`路径设置是否包括共享库目录

    vim /etc/ld.so.conf
     
    #添加如下几行
    /usr/local/lib64
    /usr/local/lib
    /usr/lib
    /usr/lib64 
    :wq

使之生效
    
    ldconfig 

#### 

https://blog.csdn.net/xiao_zhui/article/details/72556781





