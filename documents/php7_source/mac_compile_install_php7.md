# compile php7.4 on mac system

centos下编译 与mac下编译遇到问题时，解决方式不一样.

    ./configure --prefix=/usr/local/php  \
    --with-config-file-path=/usr/local/php \
    --with-curl \
    --without-iconv \
    --with-pdo-mysql \
    --with-pdo-sqlite \
    --with-freetype-dir \
    --with-gd \
    --with-kerberos \
    --with-libxml-dir \
    --with-mysqli \
    --with-openssl \
    --with-pcre-regex \
    --with-pear \
    --with-png-dir \
    --with-jpeg-dir \
    --with-xsl \
    --with-zlib \
    --with-bz2 \
    --with-mhash \
    --enable-bcmath \
    --enable-fpm \
    --enable-libxml \
    --enable-inline-optimization \
    --enable-mbregex \
    --enable-mbstring \
    --enable-opcache \
    --enable-pcntl \
    --enable-shmop \
    --enable-soap \
    --enable-sockets \
    --enable-sysvsem \
    --enable-sysvshm \
    --enable-xml --enable-zip
    
原版本.
    
    ./configure --prefix=/usr/local/php  \
    --with-config-file-path=/usr/local/php \
    --with-libdir=/usr/lib \
    --with-curl \
    --with-iconv \
    --with-pdo-mysql \
    --with-pdo-sqlite \
    --with-freetype-dir \
    --with-gd \
    --with-kerberos \
    --with-libxml-dir \
    --with-mysqli \
    --with-openssl \
    --with-pcre-regex \
    --with-pear \
    --with-png-dir \
    --with-jpeg-dir \
    --with-xmlrpc \
    --with-xsl \
    --with-zlib \
    --with-bz2 \
    --with-mhash \
    --enable-bcmath \
    --enable-fpm \
    --enable-libxml \
    --enable-inline-optimization \
    --enable-mbregex \
    --enable-mbstring \
    --enable-opcache \
    --enable-pcntl \
    --enable-shmop \
    --enable-soap \
    --enable-sockets \
    --enable-sysvsem \
    --enable-sysvshm \
    --enable-xml --enable-zip    