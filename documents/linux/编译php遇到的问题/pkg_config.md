# pgk_config

大家应该都知道用第三方库，就少不了要使用到第三方的头文件和库文件。
我们在编译、链接的时候，必须要指定这些头文件和库文件的位置。
对于一个比较大第三方库，其头文件和库文件的数量是比较多的。如果我们一个个手动地写，那将是相当麻烦的。
所以，`pkg-config`就产生了。`pkg-config`能够把这些头文件和库文件的位置指出来，给编译器使用。

其实，`pkg-config`同其他命令一样，有很多选项，不过我们一般只会用到`--libs`和`--cflags`选项。

首先要明确一点，因为`pkg-config`也只是一个命令，它是通过读取配置文件来了解这些文件位置的,而不是像一个服务一样动态的监测,统计这些数据.
所以不是你安装了一个第三方的库，`pkg-config`就能知道第三方库的头文件和库文件所在的位置。

`pkg-config`命令是通过查询`XXX.pc`文件而知道这些的。所以按照标准规范，第三方库都要提供一个或者多个属于自己的库的`.pc`文件。
接下来我们看它单独使用效果. 先找一个`openssl`库看一下.

    [sujianhui@ lib]$>pwd
    /usr/local/opt/openssl/lib
    [sujianhui@ lib]$>tree
    .
    ├── engines
    │   ├── lib4758cca.dylib
    │   ├── libaep.dylib
    │   ├── libatalla.dylib
    │   ├── libcapi.dylib
    │   ├── libchil.dylib
    │   ├── libcswift.dylib
    │   ├── libgmp.dylib
    │   ├── libgost.dylib
    │   ├── libnuron.dylib
    │   ├── libpadlock.dylib
    │   ├── libsureware.dylib
    │   └── libubsec.dylib
    ├── libcrypto.1.0.0.dylib
    ├── libcrypto.a
    ├── libcrypto.dylib -> libcrypto.1.0.0.dylib
    ├── libssl.1.0.0.dylib
    ├── libssl.a
    ├── libssl.dylib -> libssl.1.0.0.dylib
    └── pkgconfig
        ├── libcrypto.pc
        ├── libssl.pc
        └── openssl.pc
    
    2 directories, 21 files

格式 `pkg-config 包名 [选项]`

    [sujianhui@ pkgconfig]$>pkg-config openssl --libs --cflags
    -I/usr/local/Cellar/openssl/1.0.2s/include -L/usr/local/Cellar/openssl/1.0.2s/lib -lssl -lcrypto
    
    [sujianhui@ pkgconfig]$>pkg-config openssl --libs
    -L/usr/local/Cellar/openssl/1.0.2s/lib -lssl -lcrypto
    
    [sujianhui@ pkgconfig]$>pkg-config openssl --cflags
    -I/usr/local/Cellar/openssl/1.0.2s/include
    
但`pkg-config`又是如何找到所需的`.pc`文件呢？这就需要用到一个环境变量`PKG_CONFIG_PATH`来自己定制.
一般在编译时，处于临时场景的需要,我们需要临时指定该路径. `export PKG_CONFIG_PATH="/usr/local/opt/openssl/lib/pkgconfig:$PKG_CONFIG_PATH"`.
相当于追加一个需要扫描的目录.默认情况下在系统目录寻找`/usr/lib64/pkgconfig`.(或者`/usr/lib/pkgconfig`)
这环境变量(这个环境变量一般为空)写明`.pc`文件的路径，`pkg-config`命令会读取这个环境变量的内容，这样就知道`pc`文件了。 

    [sujianhui@ bin]$>echo $PKG_CONFIG_PATH
    
    [sujianhui@ bin]$>cat /usr/lib
    lib/     libexec/
     
    [sujianhui@ bin]$>cat /usr/lib/pkgconfig/
    cat: /usr/lib/pkgconfig/: Is a directory
    
    [sujianhui@ bin]$>ll /usr/lib/pkgconfig/
    total 0
    -rw-r--r--  1 root  wheel   341B Aug 18  2018 apr-1.pc
    -rw-r--r--  1 root  wheel   433B Aug 18  2018 apr-util-1.pc
    -rw-r--r--  1 root  wheel   465B Aug 18  2018 libecpg.pc
    -rw-r--r--  1 root  wheel   493B Aug 18  2018 libecpg_compat.pc
    -rw-r--r--  1 root  wheel   3.5K Aug 18  2018 libiodbc.pc
    -rw-r--r--  1 root  wheel   313B Aug 18  2018 libpcre.pc
    -rw-r--r--  1 root  wheel   301B Aug 18  2018 libpcreposix.pc
    -rw-r--r--  1 root  wheel   448B Aug 18  2018 libpgtypes.pc
    -rw-r--r--  1 root  wheel   453B Aug 18  2018 libpq.pc

现在`pkg-config`能找到我们的.pc文件。但如果有多个.pc文件，那么pkg-config又怎么能正确找到我想要的那个呢？

这就需要我们在使用pkg-config命令的时候去指定。比如

    $> gcc main.c `pkg-config --cflags --libs gtk+-2.0` -o main
    
就指定了要查找的`.pc`文件是`gtk+-2.0.pc`。又比如，有第三方库`OpenCV`，而且其对应的`pc`文件为`opencv.pc`，
那么我们在使用的时候，就要这样写`pkg-config --cflags --libs opencv`。这样`pkg-config`才会去找`opencv.pc` 文件.

但是总有那么特殊的包不按照规范.没有提供 `xxx.pc` 文件. 例如大名鼎鼎的`libiconv`.


    [sujianhui@ libiconv]$>cd /usr/local/Cellar/libiconv/1.16/
    [sujianhui@ 1.16]$>tree
    .
    ├── AUTHORS
    ├── COPYING
    ├── ChangeLog
    ├── INSTALL_RECEIPT.json
    ├── NEWS
    ├── NOTES
    ├── README
    ├── bin
    │   └── iconv
    ├── include
    │   ├── iconv.h
    │   ├── libcharset.h
    │   └── localcharset.h
    ├── lib
    │   ├── libcharset.1.dylib
    │   ├── libcharset.a
    │   ├── libcharset.dylib -> libcharset.1.dylib
    │   ├── libiconv.2.dylib
    │   ├── libiconv.a
    │   └── libiconv.dylib -> libiconv.2.dylib
    └── share
        ├── doc
        │   └── libiconv
        │       ├── iconv.1.html
        │       ├── iconv.3.html
        │       ├── iconv_close.3.html
        │       ├── iconv_open.3.html
        │       ├── iconv_open_into.3.html
        │       └── iconvctl.3.html
        └── man
            ├── man1
            │   └── iconv.1
            └── man3
                ├── iconv.3
                ├── iconv_close.3
                ├── iconv_open.3
                ├── iconv_open_into.3
                └── iconvctl.3
    
    9 directories, 29 files

同样适用brew安装的,你怎么这么特殊.对于这种未提供`.pc`文件的包,就需要走其它的加载方式了.


## 参考资料

https://www.cnblogs.com/xuyaowen/p/pkg-config-useage.html

