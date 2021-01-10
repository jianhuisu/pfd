# 解决composer网络超时问题

首先查看一下当前的 composer 全局配置地址：

	F:\>composer config -g -l repo.packagist
	[repositories.packagist.org.type] composer
	[repositories.packagist.org.url] https?://repo.packagist.org

上面的 repositories.packagist.org.url 即为全局配置的镜像地址

## 镜像配置

设置全局配置镜像地址，然后再次安装，如果等一会还是慢，继续更换地址尝试：

 - 中国全量镜像 `composer config -g repo.packagist composer https://packagist.phpcomposer.com`
 - 腾讯云 `composer config -g repos.packagist composer https://mirrors.cloud.tencent.com/composer/`
 - 阿里云 `composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/`

## 解除镜象

如果需要解除镜像并恢复到 packagist 官方源，请执行以下命令：

	composer config -g --unset repos.packagist

执行之后，composer 会利用默认值（也就是官方源）重置源地址。
