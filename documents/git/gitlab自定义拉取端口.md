# 自定义gitlab ssh拉取端口

公司gitlab自定义了ssh拉取端口.默认的22拉取失败.

    [sujianhui@ ~]$>git clone git@gitlab.xxxx.com:foo.git
    Cloning into 'foo'...
    ssh: connect to host gitlab.vhall.com port 22: Connection refused
    fatal: Could not read from remote repository.
    
每次https拉取太麻烦,怎么也得配上ssh.

    touch ~/.ssh/config
    vim config
    
    >
    [sujianhui@ .ssh]$>cat config 
    
    #gitlab
    Host gitlab gitlab.xxx.com
        HostName gitlab.xxx.com
        User git
        IdentitiesOnly yes
        PreferredAuthentications publickey
        IdentityFile /Users/sujianhui/.ssh/id_rsa
        StrictHostKeyChecking no
        ForwardAgent yes
        Port 9555
        
将公钥添加到gitlab.`IdentityFile /Users/sujianhui/.ssh/id_rsa`指定使用该私钥尽心解密.
这样可以实现跟github共用一套秘钥对.