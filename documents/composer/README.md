## composer

镜像原理：

一般情况下，安装包的数据（主要是 zip 文件）一般是从`github.com`上下载的，安装包的元数据是从`packagist.org`上下载的。
然而，由于众所周知的原因，国外的网站连接速度很慢，并且随时可能被“墙”甚至“不存在”。

“Packagist 中国全量镜像”所做的就是缓存所有安装包和元数据到国内的机房并通过国内的 CDN 进行加速，
这样就不必再去向国外的网站发起请求，从而达到加速 `composer install` 以及 `composer update` 的过程，
并且更加快速、稳定。因此，即使 `packagist.org`、`github.com` 发生故障，你仍然可以下载、更新安装包。

全局修改使用国内镜像

    composer config -g repo.packagist composer https://packagist.phpcomposer.com

解除国内镜像的使用

    composer config -g --unset repos.packagist
    
    
### composer 使用

申明依赖 `require` , 命令增加新的依赖包到当前目录的 composer.json 文件中。

    composer.phar require

获取定义的依赖到你的本地项目,读取`composer.json`解析依赖,search `package`.并将它download到`vendor`目录。 这是一个惯例把第三方的代码到一个指定的目录`vendor`。
如果是`monolog`将会创建 `vendor/monolog/monolog`目录。

包名称由供应商名称和其项目名称构成。`monolog/monolog`.供应商名称的存在则很好的解决了命名冲突的问题。它允许两个不同的人创建同样名为 json 的库。

    composer install
    
如果你正在使用Git来管理你的项目， 你可能要添加`vendor`到你的 `.gitignore` 文件中。 你不会希望将所有的代码都添加到你的版本库中。
请提交你应用程序的 composer.lock （包括 composer.json）到你的版本库中    

根据`composer.json`更新所有依赖.

    php composer.phar update
    
更新指定依赖

    php composer.phar update monolog/monolog [...]
    
查看所有可用命令列表

    composer list  

查看`composer`配置   
 
    [sujianhui@dev0529 advanced]$>composer config --list
    
列出所有可用的软件包，你可以使用 show 命令。    
    
--installed (-i): 列出已安装的依赖包。
--platform (-p): 仅列出平台软件包（PHP 与它的扩展）。
--self (-s): 仅列出当前项目信息。  

    composer show -i /  composer show --installed / composer show (default options is --installed)