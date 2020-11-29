# mac keg-only

使用brew安装bison时有个提示`keg-only`.


    [sujianhui@ php-src-php-7.4.7]$>brew install bison
    Warning: bison 3.4.1 is already installed and up-to-date
    To reinstall 3.4.1, run `brew reinstall bison`
    
    [sujianhui@ php-src-php-7.4.7]$>brew reinstall bison
    ==> Reinstalling bison 
    ==> Downloading https://homebrew.bintray.com/bottles/bison-3.4.1.mojave.bottle.tar.gz
    Already downloaded: /Users/sujianhui/Library/Caches/Homebrew/downloads/dc40c484c699b616a3bc7ed4a5b2037331458225767a8cbcd4c5152e6ea5fbcc--bison-3.4.1.mojave.bottle.tar.gz
    ==> Pouring bison-3.4.1.mojave.bottle.tar.gz
    ==> Caveats
    
    bison is keg-only, which means it was not symlinked into /usr/local,
    because some formulae require a newer version of bison.
    
    If you need to have bison first in your PATH run:
      echo 'export PATH="/usr/local/opt/bison/bin:$PATH"' >> ~/.bash_profile
    
    For compilers to find bison you may need to set:
      export LDFLAGS="-L/usr/local/opt/bison/lib"
    
    ==> Summary
    🍺  /usr/local/Cellar/bison/3.4.1: 85 files, 2.6MB

「keg-only」整个词，字面上意思现在就很清除，表示这个套件只会存放在桶子里，不会跑出桶子外。
实际上的行为是 brew 不会帮你做 symlink 到 /usr/local，避免你的原生系统内还有一套 readline 而打架，
所以提示消息说 readline 套件是 keg-only。

在说一下上边两个非常实用的命令.

    If you need to have bison first in your PATH run:
      echo 'export PATH="/usr/local/opt/bison/bin:$PATH"' >> ~/.bash_profile
    
    For compilers to find bison you may need to set:
      export LDFLAGS="-L/usr/local/opt/bison/lib"
      
这两个命令在编译时临时修改环境变量是非常的实用.      