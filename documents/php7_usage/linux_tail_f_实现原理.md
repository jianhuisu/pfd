# 如何实时监测文件的变化.

### 1 循环

这个比较好像，不停的去读文件，读到就打印出来

f = open('a','r')
print(f.read(),end='')
while True:
    try:
        print(f.read(),end='')
    except KeyboardInterrupt:
        f.close()
        break

CPU占用100%，不是一个好的选择。

###  2 select、poll

一直以来，对select和poll的用法局限于套接字，实际上，只要是文件描述符，都可以监控。
    select
    
    import select
    
    f = open('a','r')
    while True:
        try:
            rs, ws, es = select.select([f, ], [], [])
            for i in rs:
                buf = i.read()
                print(buf, end='')
        except KeyboardInterrupt:
            f.close()
            break
    
    poll
    
    import select
    
    f = open('a','r')
    poller = select.poll()
    fd_to_file = {}
    poller.register(f,select.POLLIN)
    while True:
        try:
            events = poller.poll()
            for fd,flag in events:
                if flag and fd == f.fileno():
                    print(f.read(),end='')
        except KeyboardInterrupt:
            f.close()
            break

然而，CPU占用率还是100%，原因由于作为一个普通文件描述符，时刻准备着读写事件，这使得select、poll不会阻塞，自然相当于是死循环。
此外，epoll不支持监控普通文件描述符

#### inotify

Inotify 是一个 Linux特性，它监控文件系统操作，比如读取、写入和创建。Inotify 反应灵敏，用法非常简单，并且比 cron 任务的繁忙轮询高效得多。

tail -f实际上就是inotify+select实现的，Python上一个封装的比较好的库pyinotify使用的是inotify+epoll，效率自然不低。
    
    # coding=utf-8
    import pyinotify
    
    
    class EventHandler(pyinotify.ProcessEvent):
        """事件处理"""
    
        def my_init(self, **kargs):
            self.fd = kargs['fd']
    
        def process_IN_MODIFY(self, event):
            print(self.fd.read(), end='')
    
    path = './a'
    f = open(path,'r')
    wm = pyinotify.WatchManager()
    mask = pyinotify.IN_MODIFY
    notifier = pyinotify.Notifier(wm,EventHandler(fd=f))
    wm.add_watch(path,mask,auto_add=True,rec=True)
    print('now starting monitor %s' % path)
    print(f.read(),end='')
    
    while True:
        try:
            notifier.process_events()
            if notifier.check_events():
                notifier.read_events()
        except KeyboardInterrupt:
            notifier.stop()
            break

几乎看不到CPU占用，属上乘。

可以搜索 php+inotify 实现热更新.