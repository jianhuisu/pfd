# 如何使用composer快速的组合自己的项目

## step.1 创建composer.json

 - `composer init`创建
 - 按照文档规范自己手动创建 `touch composer.json` 

例如
	
	[sujianhui@dev529 test]$>touch composer.json
	vim ...
	[sujianhui@dev529 test]$>cat composer.json 
	{
	    "name": "sujianhui/test",
	    "description": "demo",
	    "type": "project",
	    "license": "mit",
	    "authors": [
		{
		    "name": "sujianhui",
		    "email": "sujianhui@123.com"
		}
	    ],
	    "minimum-stability": "dev",
	    "require": {
	      "php": ">=7.0"
	    },
	    "autoload": {
	    "psr-4": {
	      "User\\Client\\": "src/User/Client"
	    }
	  }
	}

## 按照 composer.json 组建自己的项目 ---- 生成autoload文件


	[sujianhui@dev529 test]$>composer install
	Loading composer repositories with package information
	Updating dependencies (including require-dev)
	Nothing to install or update
	Writing lock file
	Generating autoload files

`composer install`主要生成了`autoload files`. 这个`autoload file`可是大有学问.

	[sujianhui@dev529 test]$>tree
	.
	├── composer.json
	├── composer.lock
	└── vendor
	    ├── autoload.php
	    └── composer
		├── autoload_classmap.php
		├── autoload_namespaces.php
		├── autoload_psr4.php
		├── autoload_real.php
		├── autoload_static.php
		├── ClassLoader.php
		├── installed.json
		└── LICENSE

	2 directories, 12 files

composer 的自动载入autoload可以很方便的帮我们快速的构建一套自己的框架结构，从而避免我们手动require文件.(或者造轮子，自己实现aultoload功能)
**而自动载入本身其实是利用命名空间进行对应规则或标准的路径映射，从而找到我们所需的类文件，读取载入都当前运行时**。
利用命名空间的自动载入都是`懒加载`形式的，并不会让程序变得臃肿，但懒加载在一定程度上也影响了程序的性能，要在程序规模和运行效率上做一个折中的选择。


四种模式

 - psr-0 标准 autoload_namespaces 懒加载模式 将目标目录作为基目录,以baseRoot再进行命名空间和路径的映射后继续向后加载.
 - psr-4 标准 autoload_psr4 懒加载模式，将目标目录直接映射为命名空间对应的目录继续向后加载
 - classmap 模式 autoload_classmap 懒加载模式，扫描目录下的所有类文件，支持递归扫描， 生成对应的类名=>路径的映射，当载入需要的类时直接取出路径，速度最快
 - files 模式  自动载入的文件，主要用来载入一些没办法懒加载的公共函数

## 参考资料

https://my.oschina.net/sallency/blog/893518



