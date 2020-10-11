
https://www.freebuf.com/articles/web/10369.html


>www.example.com不论是部署在tomcat5或6下按以下步骤均可强制登录：
 
 0. www.example.com/secure.jsp是一个需要登录才能访问的页面
 1. 用firefox登录该页面，然后用firebug查看cookie中的sessionId，比如等于XXX123
 2. 用IE9访问链接www.example.com/secure.jsp;jsessionid=XXX123，无法查看，但当把cookie设置为禁用时
 则可以访问到该页面。(说明先从cookie中查找sessionId,如果没有再用链接中的jsessionid，很自然的逻辑)
 3. 换台电脑用IE8访问www.example.com/secure.jsp;jsessionid=XXX123，不论是否禁用cookie均访问成功
 
 
疑问：

 1. 通过抓包工具很容易看到别人的sessionId，这样通过附加jsessionid且不是能为所欲为
 2. 为何IE8不禁用cookie也能成功，难以理解 (chrome不同版本竟然也类似于IE9/8)
 
 这个安全漏洞大家关注过么，如何解决？

#### session劫持 



PHP会自动生成一个随机的session ID，基本来说是不可能被猜测出来的，所以这方面的安全还是有一定保障的。
但是，要防止攻击者获取一个合法的session ID是相当困难的，这基本上不是开发者所能控制的。
以上只是大概地描述了session的工作机制，以及简单地阐述了一些安全措施。
但要记住，以上的方法都是能够加强安全性，不是说能够完全保护你的系统，希望读者自己再去调研相关内容。
在这个调研过程中，相信你会学到很有实际使用价值的方案。

#### token和session的区别



不是他们一直拿我当外人，原来是我自己一直把自己当外人 我以为我来这只是度个假
