# pkg-config

这是一个目录，里面包含了被其它工具链接编译时的一些必要信息.
例如我们编译php时需要引用一些外部包,就是通过PKG_CONFIG_PATH索引到pkgconfig文件而获得具体信息.

    export PKG_CONFIG_PATH="/usr/local/opt/krb5/lib/pkgconfig"

有个时候我们个人编译安装的软件并未按照操作系统规范 放置目录,就会出现这种情况.(对应英文提示就是 `if you installed software in a non-standard prefix.`)

    ./configure
    ....
    configure: error: Package requirements (openssl >= 1.0.1) were not met:
    
    No package 'openssl' found
    
    Consider adjusting the PKG_CONFIG_PATH environment variable if you
    installed software in a non-standard prefix.
    
    // 这句话非常重要 ！！！！！  PKG_CONFIG_PATH 经常出现冲突
    Alternatively, you may set the environment variables OPENSSL_CFLAGS
    and OPENSSL_LIBS to avoid the need to call pkg-config.
    See the pkg-config man page for more details.

实际上我已经安装了openssl.(十分怀疑不安装这个系统能跑么)   
    
    [sujianhui@ php-src-php-7.4.7]$>which openssl
    /usr/bin/openssl

所以,我们不需要重新安装openssl,只需要标注它的路径使得configure可以找到即可.

    [sujianhui@ php-src-php-7.4.7]$>ll /usr/local/opt/
    aircrack-ng/   bzip2/         gdb/           krb5/          libmcrypt/     libxslt/       mysql@8.0/     pcre/          python/        sqlite/        
    autoconf/      cabextract/    gdbm/          libiconv/      libmpc/        mcrypt/        nginx/         pcre1/         python3/       sqlite3/       
    autoconf@2.69/ expect/        gettext/       libidn2/       libpng/        mhash/         nmap/          pkg-config/    python@3/      tree/          
    bison/         freetype/      gmp/           libjpeg/       libunistring/  mpfr/          openssl/       pkgconfig/     re2c/          wget/          
    bison@3.4/     gcc@5/         jpeg/          libjpg/        libxml2/       mysql/         openssl@1.0/   pstree/        readline/      xz/
    
    // 果然存在            
    [sujianhui@ php-src-php-7.4.7]$>ll /usr/local/opt/openssl
    openssl/     openssl@1.0/
     
    [sujianhui@ php-src-php-7.4.7]$>ll /usr/local/opt/openssl/lib/
    engines/               libcrypto.a            libssl.1.0.0.dylib     libssl.dylib           
    libcrypto.1.0.0.dylib  libcrypto.dylib        libssl.a               pkgconfig/             
    
    [sujianhui@ php-src-php-7.4.7]$>ll /usr/local/opt/openssl/lib/pkgconfig
    total 24
    -r--r--r--  1 sujianhui  staff   307B  9 24 17:29 libcrypto.pc
    -r--r--r--  1 sujianhui  staff   308B  9 24 17:29 libssl.pc
    -r--r--r--  1 sujianhui  staff   246B  9 24 17:29 openssl.pc
    
    [sujianhui@ php-src-php-7.4.7]$>export PKG_CONFIG_PATH="/usr/local/opt/openssl/lib/pkgconfig:$PKG_CONFIG_PATH"
    
完事. 但是我更推荐另外一种方法.利用专用的环境变量

    export OPENSSL_CFLAGS="/usr/local/opt/openssl/include/" 
    export OPENSSL_LIBS="/usr/local/opt/openssl/lib/" 


类似错误

    configure: error: Package requirements (krb5-gssapi krb5) were not met:
    
    No package 'krb5-gssapi' found
    No package 'krb5' found
    
    Consider adjusting the PKG_CONFIG_PATH environment variable if you
    installed software in a non-standard prefix.
    
    Alternatively, you may set the environment variables KERBEROS_CFLAGS
    and KERBEROS_LIBS to avoid the need to call pkg-c
    
    export KERBEROS_CFLAGS="/usr/local/opt/krb5/include/" 
    export KERBEROS_LIBS="/usr/local/opt/krb5/lib/" 
    
libiconv

    configure: error: Please reinstall the iconv library.
    [sujianhui@ php-src-php-7.4.7]$>brew reinstall libiconv
    ==> Reinstalling libiconv 
    ==> Downloading https://homebrew.bintray.com/bottles/libiconv-1.16.mojave.bottle.tar.gz
    Already downloaded: /Users/sujianhui/Library/Caches/Homebrew/downloads/203933f4d9f3c2f0463012d85013a6c01bdb89fc4d435341315b4537de1dba78--libiconv-1.16.mojave.bottle.tar.gz
    ==> Pouring libiconv-1.16.mojave.bottle.tar.gz
    ==> Caveats
    libiconv is keg-only, which means it was not symlinked into /usr/local,
    because macOS already provides this software and installing another version in
    parallel can cause all kinds of trouble.
    
    If you need to have libiconv first in your PATH run:
      echo 'export PATH="/usr/local/opt/libiconv/bin:$PATH"' >> ~/.bash_profile
    
    For compilers to find libiconv you may need to set:
      export LDFLAGS="-L/usr/local/opt/libiconv/lib"
      export CPPFLAGS="-I/usr/local/opt/libiconv/include"
    
    ==> Summary
    🍺  /usr/local/Cellar/libiconv/1.16: 30 files, 2.4MB    
    