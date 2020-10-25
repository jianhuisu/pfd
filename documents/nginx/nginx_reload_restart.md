Q：有朋友疑惑，怎么在维护管理nginx的时候，看到有些资料提到重启nginx时，使用的是nginx -s reload，有时候使用的是nginx -s restart，到底是reload还是restart？

A：

`nginx -s reload`，顾名思义就是重新加载嘛，加载的是配置文件，所以如果你修改了配置文件，需要`nginx -s reload`才能生效；
`nginx -s restart`，就是单纯的重启`nginx`，不会加载修改的配置文件.所以，给你的建议就是尽量用`reload`.


