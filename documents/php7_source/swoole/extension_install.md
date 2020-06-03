## install swoole 
    
    git clont https://gitee.com/swoole/swoole?_from=gitee_search
    sudo yum install -y autoconf
    cd swoole-src && \
    phpize && \
    ./configure && \
    make && sudo make install
    
在`php.ini`中加入一行`extension=swoole.so`来启用`Swoole`扩展.
    
查看是否安装成功
    
 - CLI模式 `php -m | grep swoole`
 - CGI模式 `phpinfo`
 
查看swoole的安装版本
    
    [sujianhui@dev529 php]$>php -h
    ...    
      --ini            Show configuration file names
      --rf <name>      Show information about function <name>.
      --rc <name>      Show information about class <name>.
      --re <name>      Show information about extension <name>.
      --rz <name>      Show information about Zend extension <name>.
      --ri <name>      Show configuration for extension <name>.

    [sujianhui@dev529 php]$>php --ri swoole
    
    swoole
    
    Swoole => enabled
    Author => Swoole Team <team@swoole.com>
    Version => 4.5.1
    Built => May 17 2020 22:30:02
    coroutine => enabled
    epoll => enabled
    eventfd => enabled
    signalfd => enabled
    cpu_affinity => enabled
    spinlock => enabled
    rwlock => enabled
    pcre => enabled
    zlib => 1.2.7
    mutex_timedlock => enabled
    pthread_barrier => enabled
    futex => enabled
    async_redis => enabled
    
    Directive => Local Value => Master Value
    swoole.enable_coroutine => On => On
    swoole.enable_library => On => On
    swoole.enable_preemptive_scheduler => Off => Off
    swoole.display_errors => On => On
    swoole.use_shortname => On => On
    swoole.unixsock_buffer_size => 8388608 => 8388608

    
#### `PHP扩展`与`Zend扩展`区别

通常在`php.ini`中，通过`extension=*`加载的扩展我们称为PHP扩展，通过`zend_extension=*`加载的扩展我们称为Zend扩展.
但从源码的角度来讲，PHP扩展应该称为“模块”（源码中以module命名），而Zend扩展称为“扩展”（源码中以extension命名）。
两者最大的区别在于向引擎注册的钩子。少数的扩展，例如xdebug、opcache，既是PHP扩展，也是Zend扩展，但它们在`php.ini`中的加载方式得用`zend_extension=*，`

 - zend_extension这个结构体就提供了hook到Zend引擎的钩子.
 - zend_module_entry这个结构体提供了hook面向用户层面提供一些C实现的PHP函数的钩子.

http://yangxikun.github.io/php/2016/07/10/php-zend-extension.html

#### 为什么有的扩展处于注释状态,但实际上被启用 

与`php`一起编译的扩展，就算你在`php.ini`中注释该`extension`，无法将其禁用。非要禁用的话可以考虑重新编译php。
而后续编译追加的`extension`注释后则会生效.所以，我们可以在编译安装php时就安装`swoole` through `--enable-swoole`    