# 如何高大上的解决 404

配置完vhost.conf后,访问域名返回404.依次检查

 - selinux是否关闭
 - 域名路径是否正确
 - hostname与ip是否对应

这几处都没有问题,那该怎么办呢？

nginx的进程模型是 `master-worker`模式.由worker进行负责具体的cgi请求解析.

	[sujianhui@dev529 public]$>ps aux | grep nginx
	root      9977  0.0  0.0  47496  2444 ?        Ss   15:13   0:00 nginx: master process /usr/sbin/nginx -c /etc/nginx/nginx.conf
	sujianh+ 19271  0.0  0.0  49584  2732 ?        S    20:27   0:00 nginx: worker process
	sujianh+ 19992  0.0  0.0 112720   964 pts/0    S+   20:44   0:00 grep --color=auto nginx

如果能追踪到worker进程load哪个文件就好了,去验证一下文件存在不存在就完事，问题是如何追踪呢？

linux上有一个工具`strace`,可以监测进程发起的系统调用.也就是说,操作系统的六大基本操作我们都可以看到.其中有一个系统调用`stat`,获取文件的元信息.
所以我们监测这个即可.

因为我只开了一个worker进程.所以只strace一个worker进程即可.

	[sujianhui@dev529 network_security]$>strace -p 19271
	strace: Process 19271 attached
	epoll_wait(11, [{EPOLLIN, {u32=261448992, u64=94395052680480}}], 512, -1) = 1
	accept4(7, {sa_family=AF_INET, sin_port=htons(38612), sin_addr=inet_addr("127.0.0.1")}, [16], SOCK_NONBLOCK) = 4
	epoll_ctl(11, EPOLL_CTL_ADD, 4, {EPOLLIN|EPOLLRDHUP|EPOLLET, {u32=261449689, u64=94395052681177}}) = 0
	epoll_wait(11, [{EPOLLIN, {u32=261449689, u64=94395052681177}}], 512, 60000) = 1
	recvfrom(4, "GET / HTTP/1.1\r\nHost: local.lara"..., 1024, 0, NULL, NULL) = 661
	stat("/home/sujianhui/PhpstormProjects/blog/public/", {st_mode=S_IFDIR|0775, st_size=95, ...}) = 0
	stat("/home/sujianhui/PhpstormProjects/blog/public/", {st_mode=S_IFDIR|0775, st_size=95, ...}) = 0
	stat("/home/sujianhui/PhpstormProjects/blog/public/index.php", {st_mode=S_IFREG|0664, st_size=1731, ...}) = 0
	epoll_ctl(11, EPOLL_CTL_MOD, 4, {EPOLLIN|EPOLLOUT|EPOLLRDHUP|EPOLLET, {u32=261449689, u64=94395052681177}}) = 0
	getsockname(4, {sa_family=AF_INET, sin_port=htons(80), sin_addr=inet_addr("127.0.0.1")}, [16]) = 0
	socket(AF_INET, SOCK_STREAM, IPPROTO_IP) = 5
	ioctl(5, FIONBIO, [1])                  = 0
	epoll_ctl(11, EPOLL_CTL_ADD, 5, {EPOLLIN|EPOLLOUT|EPOLLRDHUP|EPOLLET, {u32=261449457, u64=94395052680945}}) = 0
	connect(5, {sa_family=AF_INET, sin_port=htons(9000), sin_addr=inet_addr("127.0.0.1")}, 16) = -1 EINPROGRESS 
	epoll_wait(11, [], 512, 60000)          = 0
	...
	...
	close(9)                                = 0
	epoll_wait(11, [], 512, 4943)           = 0
	close(4)                                = 0
	epoll_wait(11, ^Cstrace: Process 19271 detached
	 <detached ...>

提取关键信息

	`stat("/home/sujianhui/PhpstormProjects/blog/public/index.php", {st_mode=S_IFREG|0664, st_size=1731, ...}) = 0`

追踪`stat`可以发现worker寻找`/home/sujianhui/PhpstormProjects/blog/public/index.php`.到此问题解决.403问题也可以依靠这个思路解决.



