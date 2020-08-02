## 查询分析工具 pt-query-digest

pt-query-digest是用于分析mysql慢查询的一个工具，它可以分析binlog、General log、slowlog，
也可以通过SHOWPROCESSLIST或者通过tcpdump抓取的MySQL协议数据来进行分析。
可以把分析结果输出到文件中，分析过程是先对查询语句的条件进行参数化，然后对参数化以后的查询进行分组统计，
统计出各查询的执行时间、次数、占比等，可以借助分析结果找出问题进行优化。

