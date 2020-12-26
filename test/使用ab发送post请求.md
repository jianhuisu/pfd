# 使用ab发送post请求.

    #!/usr/bin/env bash
    
    # ab -c 10 -n 1000 http://dispatch.vhall.com/v3/interacts/room/get-inav-tool-status
    
    ab -c 1 -n 1 -v 4 -p './post.txt' \
      -H 'Content-Type: multipart/form-data' \
      -T 'application/x-www-form-urlencoded' \
      -H 'Connection: keep-alive' \
      -H 'request-id: b43d8280-475e-11eb-9039-533490961477' \
      -H 'platform: 17' \
      -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36' \
      -H 'token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2MDg4MDE5NzMsImV4cCI6MTYxMTM5Mzk3MywidXNlcl9pZCI6MTY0MjI3NDksInBsYXRmb3JtIjoiMTcifQ.ueX_sbjhDgFP0XhkItB6iWL9DMAVDpprNQivmgJsAbk' \
      -H 'Content-Type: Content-Type: multipart/form-data' \
      -H 'Accept: */*' \
      -H 'Origin: https://t.e.vhall.com' \
      -H 'Sec-Fetch-Site: same-site' \
      -H 'Sec-Fetch-Mode: cors' \
      -H 'Sec-Fetch-Dest: empty' \
      http://dispatch.vhall.com/v3/interacts/gift/shared-gift-list?hello=asdfasdf

比较实用的三个参数

 - `-v` `Set verbosity level` . - 4 and above prints information on headers, 3 and above prints response codes (404, 200, etc.), 2 and above prints warnings and info.
 - `-T` ` set content-type` . 设置请求方式. 使用方式 `-T 'application/x-www-form-urlencoded'` 不能用 ` -H 'Content-Type: multipart/form-data'` 方式代替
 - `-H` `custom-header` . Add Arbitrary header line, Inserted after all normal header lines. (repeatable).

使用 `ab --help` 查看详情. 

## 参考资料

http://httpd.apache.org/docs/current/programs/ab.html#output