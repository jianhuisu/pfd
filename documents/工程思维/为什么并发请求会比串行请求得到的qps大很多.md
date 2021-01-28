# 

并发数为1时.QPS为1200左右.

    ab -c 1 -n 8000 http://local.pfd.com/

    Concurrency Level:      1
    Time taken for tests:   6.476 seconds
    Complete requests:      8000
    Failed requests:        0
    Total transferred:      1320000 bytes
    HTML transferred:       8000 bytes
    Requests per second:    1235.36 [#/sec] (mean)
    Time per request:       0.809 [ms] (mean)
    Time per request:       0.809 [ms] (mean, across all concurrent requests)
    Transfer rate:          199.06 [Kbytes/sec] received

并发数为10时.QPS为3200左右.

    ab -c 10 -n 8000 http://local.pfd.com/    

    Concurrency Level:      10
    Time taken for tests:   2.556 seconds
    Complete requests:      8000
    Failed requests:        0
    Total transferred:      1320000 bytes
    HTML transferred:       8000 bytes
    Requests per second:    3130.02 [#/sec] (mean)
    Time per request:       3.195 [ms] (mean)
    Time per request:       0.319 [ms] (mean, across all concurrent requests)
    Transfer rate:          504.35 [Kbytes/sec] received

并发数为30时.QPS为800左右.

    Concurrency Level:      30
    Time taken for tests:   9.313 seconds
    Complete requests:      8000
    Failed requests:        0
    Total transferred:      1320000 bytes
    HTML transferred:       8000 bytes
    Requests per second:    859.01 [#/sec] (mean)
    Time per request:       34.924 [ms] (mean)
    Time per request:       1.164 [ms] (mean, across all concurrent requests)
    Transfer rate:          138.41 [Kbytes/sec] received

### 总结

服务器处理并发请求时,利用IO等待时空闲的CPU时间片来处理其它请求,提高了服务器的并发性.但是两者并不是正比例关系.
