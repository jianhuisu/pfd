## nginx


#### find nginx.conf

    [sujianhui@dev529 ~]$>nginx -V | grep nginx.conf
    nginx version: nginx/1.18.0
    built by gcc 4.8.5 20150623 (Red Hat 4.8.5-39) (GCC) 
    built with OpenSSL 1.0.2k-fips  26 Jan 2017
    TLS SNI support enabled
    configure arguments: 
        --prefix=/etc/nginx 
        --sbin-path=/usr/sbin/nginx 
        --modules-path=/usr/lib64/nginx/modules 
        --conf-path=/etc/nginx/nginx.conf 
        --error-log-path=/var/log/nginx/error.log
        ...
        
 