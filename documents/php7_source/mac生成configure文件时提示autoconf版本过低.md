# 源码编译php时发现没有configure文件. 

源码编译php时发现没有configure.使用autoconf生成configure时发现版本过低.

    [sujianhui@ php-src-php-7.4.7]$>./buildconf --force
    buildconf: Checking installation
    buildconf: autoconf version 2.65 found.
               You need autoconf version 2.68 or newer installed
               to build PHP from Git.
    
    [sujianhui@ php-src-php-7.4.7]$>autoconf -V
    autoconf (GNU Autoconf) 2.65
    Copyright (C) 2009 Free Software Foundation, Inc.
    License GPLv3+/Autoconf: GNU GPL version 3 or later
    <http://gnu.org/licenses/gpl.html>, <http://gnu.org/licenses/exceptions.html>
    This is free software: you are free to change and redistribute it.
    There is NO WARRANTY, to the extent permitted by law.
    
    Written by David J. MacKenzie and Akim Demaille.

去看一眼什么情况. cd `which autoconf`. 

    [sujianhui@ bin]$>ls -l auto*
    -rwxr-xr-x  1 sujianhui  admin    14K  5 15  2019 autoconf*
    lrwxr-xr-x  1 sujianhui  admin    38B  6  3  2019 autoexpect@ -> ../Cellar/expect/5.45.4/bin/autoexpect
    -rwxr-xr-x  1 sujianhui  admin   8.4K  5 15  2019 autoheader*
    -rwxr-xr-x  1 sujianhui  admin    31K  5 15  2019 autom4te*
    -rwxr-xr-x  2 sujianhui  admin   251K  5 15  2019 automake*
    -rwxr-xr-x  2 sujianhui  admin   251K  5 15  2019 automake-1.11*
    lrwxr-xr-x  1 sujianhui  admin    38B  6  3  2019 autopasswd@ -> ../Cellar/expect/5.45.4/bin/autopasswd
    -rwxr-xr-x  1 sujianhui  admin    20K  5 15  2019 autoreconf*
    -rwxr-xr-x  1 sujianhui  admin    17K  5 15  2019 autoscan*
    -rwxr-xr-x  1 sujianhui  admin    33K  5 15  2019 autoupdate*
    
一个家族啊,这我也不敢单独升级.看看brew好使不.    
    
    [sujianhui@ bin]$>brew update autoconf
    Error: This command updates brew itself, and does not take formula names.
    Use 'brew upgrade autoconf' instead.
    
    [sujianhui@ bin]$>brew upgrade autoconf
    Error: autoconf not installed
    
现在系统里边存在的autoconf不是使用brew安装的.算了我用brew重新安装一个吧. 然后将低版的删除掉.
    
    [sujianhui@ bin]$>brew install autoconf
    ==> Downloading https://homebrew.bintray.com/bottles/autoconf-2.69.mojave.bottle.4.tar.gz
    ==> Downloading from https://d29vzk4ow07wi7.cloudfront.net/9724736d34773b6e41e2434ffa28fe79feccccf7b7786e54671441ca75115cdb?response-content-disposition=attachment%3Bfilename%3D%22autoconf-2.69.mojave.bottle.4.tar.gz%22&Policy=eyJTdGF0ZW1
    ######################################################################## 100.0%
    ==> Pouring autoconf-2.69.mojave.bottle.4.tar.gz
    Error: The `brew link` step did not complete successfully
    The formula built, but is not symlinked into /usr/local
    Could not symlink bin/autoconf
    Target /usr/local/bin/autoconf
    already exists. You may want to remove it:
      rm '/usr/local/bin/autoconf'
    
    To force the link and overwrite all conflicting files:
      brew link --overwrite autoconf
    
    To list all files that would be deleted:
      brew link --overwrite --dry-run autoconf
    
    Possible conflicting files are:
    /usr/local/bin/autoconf
    /usr/local/bin/autoheader
    /usr/local/bin/autom4te
    /usr/local/bin/autoreconf
    /usr/local/bin/autoscan
    /usr/local/bin/autoupdate
    /usr/local/bin/ifnames
    /usr/local/share/autoconf/Autom4te/C4che.pm
    /usr/local/share/autoconf/Autom4te/ChannelDefs.pm
    /usr/local/share/autoconf/Autom4te/Channels.pm
    /usr/local/share/autoconf/Autom4te/Configure_ac.pm
    /usr/local/share/autoconf/Autom4te/FileUtils.pm
    /usr/local/share/autoconf/Autom4te/General.pm
    /usr/local/share/autoconf/Autom4te/Request.pm
    /usr/local/share/autoconf/Autom4te/XFile.pm
    /usr/local/share/autoconf/INSTALL
    /usr/local/share/autoconf/autoconf/autoconf.m4
    /usr/local/share/autoconf/autoconf/autoconf.m4f
    /usr/local/share/autoconf/autoconf/autoheader.m4
    /usr/local/share/autoconf/autoconf/autoscan.m4
    /usr/local/share/autoconf/autoconf/autotest.m4
    /usr/local/share/autoconf/autoconf/autoupdate.m4
    /usr/local/share/autoconf/autoconf/c.m4
    /usr/local/share/autoconf/autoconf/erlang.m4
    /usr/local/share/autoconf/autoconf/fortran.m4
    /usr/local/share/autoconf/autoconf/functions.m4
    /usr/local/share/autoconf/autoconf/general.m4
    /usr/local/share/autoconf/autoconf/headers.m4
    /usr/local/share/autoconf/autoconf/lang.m4
    /usr/local/share/autoconf/autoconf/libs.m4
    /usr/local/share/autoconf/autoconf/oldnames.m4
    /usr/local/share/autoconf/autoconf/programs.m4
    /usr/local/share/autoconf/autoconf/specific.m4
    /usr/local/share/autoconf/autoconf/status.m4
    /usr/local/share/autoconf/autoconf/types.m4
    /usr/local/share/autoconf/autom4te.cfg
    /usr/local/share/autoconf/autoscan/autoscan.list
    /usr/local/share/autoconf/autotest/autotest.m4
    /usr/local/share/autoconf/autotest/autotest.m4f
    /usr/local/share/autoconf/autotest/general.m4
    /usr/local/share/autoconf/autotest/specific.m4
    /usr/local/share/autoconf/m4sugar/foreach.m4
    /usr/local/share/autoconf/m4sugar/m4sh.m4
    /usr/local/share/autoconf/m4sugar/m4sh.m4f
    /usr/local/share/autoconf/m4sugar/m4sugar.m4
    /usr/local/share/autoconf/m4sugar/m4sugar.m4f
    /usr/local/share/autoconf/m4sugar/version.m4
    /usr/local/share/info/autoconf.info
    /usr/local/share/man/man1/autoconf.1
    /usr/local/share/man/man1/autoheader.1
    /usr/local/share/man/man1/autom4te.1
    /usr/local/share/man/man1/autoreconf.1
    /usr/local/share/man/man1/autoscan.1
    /usr/local/share/man/man1/autoupdate.1
    /usr/local/share/man/man1/config.guess.1
    /usr/local/share/man/man1/config.sub.1
    /usr/local/share/man/man1/ifnames.1
    ==> Caveats
    Emacs Lisp files have been installed to:
      /usr/local/share/emacs/site-lisp/autoconf
    ==> Summary
    🍺  /usr/local/Cellar/autoconf/2.69: 71 files, 3.0MB
    ==> `brew cleanup` has not been run in 30 days, running now...
    Removing: /Users/sujianhui/Library/Logs/Homebrew/libiconv... (64B)
    Removing: /Users/sujianhui/Library/Logs/Homebrew/wget... (64B)
    Removing: /Users/sujianhui/Library/Logs/Homebrew/libidn2... (64B)
    Removing: /Users/sujianhui/Library/Logs/Homebrew/libxml2... (64B)
    Removing: /Users/sujianhui/Library/Logs/Homebrew/libpng... (64B)
    Removing: /Users/sujianhui/Library/Logs/Homebrew/libxslt... (64B)
    Removing: /Users/sujianhui/Library/Logs/Homebrew/freetype... (64B)
    Removing: /Users/sujianhui/Library/Logs/Homebrew/mhash... (64B)
    Removing: /Users/sujianhui/Library/Logs/Homebrew/gettext... (64B)
    Removing: /Users/sujianhui/Library/Logs/Homebrew/mcrypt... (64B)
    Removing: /Users/sujianhui/Library/Logs/Homebrew/jpeg... (64B)
    Removing: /Users/sujianhui/Library/Logs/Homebrew/openssl... (64B)
    Removing: /Users/sujianhui/Library/Logs/Homebrew/bzip2... (64B)
    Pruned 0 symbolic links and 2 directories from /usr/local
    
完事了？ 看一下版本，    
    
    [sujianhui@ bin]$>which autoconf
    /usr/local/bin/autoconf
    [sujianhui@ bin]$>autoconf -V
    autoconf (GNU Autoconf) 2.65
    Copyright (C) 2009 Free Software Foundation, Inc.
    License GPLv3+/Autoconf: GNU GPL version 3 or later
    <http://gnu.org/licenses/gpl.html>, <http://gnu.org/licenses/exceptions.html>
    This is free software: you are free to change and redistribute it.
    There is NO WARRANTY, to the extent permitted by law.
    
    Written by David J. MacKenzie and Akim Demaille.
    
还是旧版本啊,是没有更新环境变量还是压根就没安装成功.看看刚才`brew install`的提示信息(编译软件时出问题一定要看错误提示信息!!!!!)

    Error: The `brew link` step did not complete successfully
    The formula built, but is not symlinked into /usr/local
    Could not symlink bin/autoconf
    Target /usr/local/bin/autoconf
    already exists. You may want to remove it:
      rm '/usr/local/bin/autoconf'
    
    To force the link and overwrite all conflicting files:
      brew link --overwrite autoconf
    
    To list all files that would be deleted:
      brew link --overwrite --dry-run autoconf    

白纸黑字,虽然编译成功,但是链接失败.(就是创建软链时失败)，因为旧版本的autoconf占着坑，brew 也不敢随便`overwrite`    
看看要覆写哪些文件
    
    [sujianhui@ bin]$>brew link --overwrite --dry-run autoconf
    Would remove:
    /usr/local/bin/autoconf
    /usr/local/bin/autoheader
    /usr/local/bin/autom4te
    /usr/local/bin/autoreconf
    /usr/local/bin/autoscan
    /usr/local/bin/autoupdate
    /usr/local/bin/ifnames
    /usr/local/share/autoconf/Autom4te/C4che.pm
    /usr/local/share/autoconf/Autom4te/ChannelDefs.pm
    /usr/local/share/autoconf/Autom4te/Channels.pm
    /usr/local/share/autoconf/Autom4te/Configure_ac.pm
    /usr/local/share/autoconf/Autom4te/FileUtils.pm
    /usr/local/share/autoconf/Autom4te/General.pm
    /usr/local/share/autoconf/Autom4te/Request.pm
    /usr/local/share/autoconf/Autom4te/XFile.pm
    /usr/local/share/autoconf/INSTALL
    /usr/local/share/autoconf/autoconf/autoconf.m4
    /usr/local/share/autoconf/autoconf/autoconf.m4f
    /usr/local/share/autoconf/autoconf/autoheader.m4
    /usr/local/share/autoconf/autoconf/autoscan.m4
    /usr/local/share/autoconf/autoconf/autotest.m4
    /usr/local/share/autoconf/autoconf/autoupdate.m4
    /usr/local/share/autoconf/autoconf/c.m4
    /usr/local/share/autoconf/autoconf/erlang.m4
    /usr/local/share/autoconf/autoconf/fortran.m4
    /usr/local/share/autoconf/autoconf/functions.m4
    /usr/local/share/autoconf/autoconf/general.m4
    /usr/local/share/autoconf/autoconf/headers.m4
    /usr/local/share/autoconf/autoconf/lang.m4
    /usr/local/share/autoconf/autoconf/libs.m4
    /usr/local/share/autoconf/autoconf/oldnames.m4
    /usr/local/share/autoconf/autoconf/programs.m4
    /usr/local/share/autoconf/autoconf/specific.m4
    /usr/local/share/autoconf/autoconf/status.m4
    /usr/local/share/autoconf/autoconf/types.m4
    /usr/local/share/autoconf/autom4te.cfg
    /usr/local/share/autoconf/autoscan/autoscan.list
    /usr/local/share/autoconf/autotest/autotest.m4
    /usr/local/share/autoconf/autotest/autotest.m4f
    /usr/local/share/autoconf/autotest/general.m4
    /usr/local/share/autoconf/autotest/specific.m4
    /usr/local/share/autoconf/m4sugar/foreach.m4
    /usr/local/share/autoconf/m4sugar/m4sh.m4
    /usr/local/share/autoconf/m4sugar/m4sh.m4f
    /usr/local/share/autoconf/m4sugar/m4sugar.m4
    /usr/local/share/autoconf/m4sugar/m4sugar.m4f
    /usr/local/share/autoconf/m4sugar/version.m4
    /usr/local/share/info/autoconf.info
    /usr/local/share/man/man1/autoconf.1
    /usr/local/share/man/man1/autoheader.1
    /usr/local/share/man/man1/autom4te.1
    /usr/local/share/man/man1/autoreconf.1
    /usr/local/share/man/man1/autoscan.1
    /usr/local/share/man/man1/autoupdate.1
    /usr/local/share/man/man1/config.guess.1
    /usr/local/share/man/man1/config.sub.1
    /usr/local/share/man/man1/ifnames.1

这个感情好,都是一个家族的,灭九族了.毫不犹豫.overwrite    
    
    [sujianhui@ bin]$>brew link --overwrite autoconf
    Linking /usr/local/Cellar/autoconf/2.69... 60 symlinks created
    [sujianhui@ bin]$>autoconf -V
    autoconf (GNU Autoconf) 2.69
    Copyright (C) 2012 Free Software Foundation, Inc.
    License GPLv3+/Autoconf: GNU GPL version 3 or later
    <http://gnu.org/licenses/gpl.html>, <http://gnu.org/licenses/exceptions.html>
    This is free software: you are free to change and redistribute it.
    There is NO WARRANTY, to the extent permitted by law.
    
    Written by David J. MacKenzie and Akim Demaille.
    [sujianhui@ bin]$>

完事.去php源码目录看一下

    [sujianhui@ php-src-php-7.4.7]$>autoconf
    [sujianhui@ php-src-php-7.4.7]$>ll conf*
    -rwxr-xr-x  1 sujianhui  staff   2.3M 11 29 10:53 configure*
    -rw-r--r--@ 1 sujianhui  staff    43K  6  9 18:57 configure.ac

车房都有了.美滋滋