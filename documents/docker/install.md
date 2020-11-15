
docker各个容器是共享操作系统内核的,而VM的完全模拟一套操作系统.

普通用户没有使用docker的权限.

	[sujianhui@dev529 C]$>docker ps
	Got permission denied while trying to connect to the Docker daemon socket at unix:///var/run/docker.sock: 
	Get http://%2Fvar%2Frun%2Fdocker.sock/v1.26/containers/json: dial unix /var/run/docker.sock: connect: permission denied

	
我们发现,docker客户端与服务端通过socket的方式进行通信.

	[sujianhui@dev529 C]$>ls -la /var/run/ | egrep '^s'
	srw-rw----   1 root           root              0 Nov 15 22:35 docker.sock // 这个...
	srw-rw-rw-   1 root           root              0 Nov 15 21:59 gssproxy.sock
	srw-rw-rw-   1 root           root              0 Nov 15 21:59 rpcbind.sock


todo
