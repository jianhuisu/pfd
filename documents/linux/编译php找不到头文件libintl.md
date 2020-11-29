# configure: error: Cannot locate header file libintl.h 错误的解决方法

MAC OS 上编译 PHP 时，在 configure 配置阶段出现如题所示错误。找不到 libintl.h 头文件。

解决方法如下：

查看是否已经安装`gettext`否则安装`gettext`.

    brew install gettext

编辑 configure 文件,将：

Default

    for i in $PHP_GETTEXT /usr/local /usr ; do

更改为：(前提检查一下`/usr/local/opt/gettext`是否存在.)

    for i in $PHP_GETTEXT /usr/local /usr /usr/local/opt/gettext; do    

