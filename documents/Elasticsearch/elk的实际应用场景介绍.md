## elk的实际用场景简介

elk:

 - elasticsearch  
 - logstash 
 - kibnan 可拔那

elasticsearch构建在Lucene之上，过滤器语法和Lucene相同.
> es是在lucene搜索引擎之上建立的 搜索工具. 并且 es 的搜索语法(也就是过滤器语法) 与 lucene 保持一致.

kibnan 在es基础之上构建的可视化查询工具. 
> kibnan与es是共生模式,协同工作.而不是要取代es. es更像是一个命令行工具. 虽然功能十分强大,但是使用起来需要我们手动
> 拼装参数，使用门槛比较高.所以 kibnan搞了一个web系统，我们在kibnan界面上点啊点就能组合出条件. 点击搜索就能
> 搜索出我们关系的数据. 就像我们在淘宝上 选择价格区间，品牌，款式，颜色等条件然后点击搜索一样.

logstash

Logstash是一个开源数据收集引擎，具有实时管道功能。Logstash可以动态地将来自不同数据源的数据统一起来，并将数据标准化到你所选择的目的地。
>各个服务器节点生成的日志数据是不规则的.非结构化的.Logstash像一个卧底一样被安插在每个节点服务器上. Logstash根据配置文件，从丰富多彩的数据源
> (也就是各种各样的非结构化的数据源中)派生出结构化数据，派生出的结构化数据是标准的.这些数据拥有类似的结构.就像mysql中一行数据一样.
> 然后logstash将这些标准化的数据发送到我们的存储系统(例如但不限于elasticsearch)

数据往往以各种各样的形式，或分散或集中地存在于很多系统中。Logstash 支持各种输入选择 ，可以在同一时间从众多常用来源捕捉事件。
能够以连续的流式传输方式，轻松地从您的日志、指标、Web 应用、数据存储以及各种 AWS 服务采集数据。

数据从源传输到存储库的过程中，Logstash 过滤器能够解析各个事件，识别已命名的字段以构建结构，并将它们转换成通用格式，以便更轻松、更快速地分析和实现商业价值。

Logstash 能够动态地转换和解析数据，不受格式或复杂度的影响，例如:

 - 利用 Grok 从非结构化数据中派生出结构.(Grok是一个过滤器插件.logstash将过滤器设计为插件的模式,便于对过滤器语法进行扩展)
 - 从 IP 地址破译出地理坐标
 - 将 PII 数据匿名化，完全排除敏感字段
 - 整体处理不受数据源、格式或架构的影响

尽管 Elasticsearch 是我们的首选输出方向，能够为我们的搜索和分析带来无限可能，但它并非唯一选择。 
Logstash 提供众多输出选择，您可以将数据发送到您要指定的地方，并且能够灵活地解锁众多下游用例。

这个实现日志过滤的的工具称为:Logstash管道

 1. 输入插件从数据源那里消费数据，
 2. 过滤器插件根据你的期望修改数据，
 3. 输出插件将数据写入目的地

在实际应用中,我们首先创建一个Logstash管道，并且使用Filebeat将Apache Web日志作为input，解析这些日志，
然后将解析的数据写到一个Elasticsearch集群中。你将在配置文件中定义管道，而不是在命令行中定义管道配置。

在你创建Logstash管道之前，你需要先配置Filebeat来发送日志行到Logstash。Filebeat客户端是一个轻量级的、资源友好的工具， 
它从服务器上的文件中收集日志，并将这些日志转发到你的Logstash实例以进行处理。Filebeat设计就是为了可靠性和低延迟。
Filebeat在主机上占用的资源很少，而且Beats input插件将对Logstash实例的资源需求降到最低。

filebeat的功能与logstash功能上是有重合的,但是两者的擅长能力各有倾斜.(详细见两者的压测比较)

（画外音：注意，在一个典型的用例中，Filebeat和Logstash实例是分开的，它们分别运行在不同的机器上。在本教程中，Logstash和Filebeat在同一台机器上运行。）

Logstash用http协议连接到Elasticsearch，而且假设Logstash和Elasticsearch允许在同一台机器上。你也可以指定一个远程的Elasticsearch实例，
比如`host=>["es-machine:9092"]`.

## 参考资料 

https://www.cnblogs.com/davidwang456/p/7795251.html
https://www.cnblogs.com/cjsblog/p/9459781.html
