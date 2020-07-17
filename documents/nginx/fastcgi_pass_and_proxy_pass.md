## fastcgi_pass_and_proxy_pass

浪费了两个小时 ...

    server {
    
    	listen       80;
    	server_name  local.dev.com;
    	root   /home/sujianhui/PhpstormProjects/pfd/;
            index  index.php;
    
    	location ~* \.php$ {
    
    		include        /etc/nginx/fastcgi_params;	
    		fastcgi_pass   127.0.0.1:9000;
    		# proxy_pass   http://127.0.0.1;
    		fastcgi_param  SCRIPT_NAME $fastcgi_script_name;
    		fastcgi_param  PATH_INFO $fastcgi_path_info;
    		fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
    		fastcgi_index  index.php;
    	}
    
    }
    
    
千万要注意关键字使用正确:    

 - 网关代理`fastcgi_pass`
 - 反向代理`proxy_pass`