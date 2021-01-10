# composer

composer init 时提示我输入帐号密码

	Enter package # to add, or the complete package name if it is not listed: #0
	Enter the version constraint to require (or leave blank to use the latest version): 
	    Authentication required (repo.packagist.org):
	      Username: 
	      Password: 

这个帐号密码什么鬼,原来没有注册过帐号密码.

	[sujianhui@dev529 test]$>composer init

		                                    
	  Welcome to the Composer config generator  
		                                    


	This command will guide you through creating your composer.json config.

	Package name (<vendor>/<name>) [sujianhui/test]: 
	Description []: 
	Author [sujianhui <1051034413@qq.com>, n to skip]: 
	Minimum Stability []: 
	Package Type (e.g. library, project, metapackage, composer-plugin) []: project
	License []: 

	Define your dependencies.

	Would you like to define your dependencies (require) interactively [yes]? 
	Search for a package: autoload

	Found 15 packages matching autoload

	   [0] composer/composer 
	   [1] nette/robot-loader 
	   [2] laminas/laminas-zendframework-bridge 
	   ...

	Enter package # to add, or the complete package name if it is not listed: #0
	Enter the version constraint to require (or leave blank to use the latest version): 
	Composer could not find a composer.json file in /home/sujianhui/PhpstormProjects/test
	To initialize a project, please create a composer.json file as described in the https://getcomposer.org/ "Getting Started" section

问题出现在 `Package Type (e.g. library, project, metapackage, composer-plugin) []: project`
如果我们要创建一个pkg.自然需要发布到云端,就需要个一个平台的帐号密码.而我的目的是使用composer提供的autoload能力,并非要构建一个pkg. 标注工程类型为`project`则可以跳过验证.
