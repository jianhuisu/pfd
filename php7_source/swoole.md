## swoole

shi jie shang mei you po jie bu liao de  ruan jian , zhi you bu zhi de po jie de ruan jian.

协程的创建、切换、挂起、销毁全部为内存操作，消耗是非常低的
程序仅启动了一个1个进程，就可以并发处理大量请求。
程序的性能基本上与异步回调方式相同，但是代码完全是同步编写的

通道与 PHP 的 Array 类似，仅占用内存，没有其他额外的资源申请，所有操作均为内存操作，无 IO 消耗
底层使用 PHP 引用计数实现，无内存拷贝。即使是传递巨大字符串或数组也不会产生额外性能消耗
channel 基于引用计数实现，是零拷贝的

定时器是内存操作，无 IO 消耗

CSP 编程模型
CSP ，全称:Communicating Sequential Process ，翻译成中文是，通信顺序进程，
最初于Tony Hoare的1977年的论文中被描述，影响了许多编程语言的设计。用于描述两个的ulinix并发的实体通过共享的通讯管道（channel)进行通信的 并发模型。在该模型中，
channel 是比较重要的对象，它并不关注发送消息的实体，而只关心与发送消息时实体使用的channel