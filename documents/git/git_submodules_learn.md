## git submodule 

    app
    vhall-core  [一个sub modules]
    
1 拉取代码 
2、切换到master 分支 
3、执行 git submodule init 
4、执行 git submodule update 
5、新建别的分支 
6、切换到新建分支，开发, done!    

在开发过程中发现.

git status后提示：submodule 有 modified () new commits问题

如果你的git仓库有依赖subModule，然后，sumModule有更新时，本地的gitsubmodules就变为旧版本.

这时你没有执行git submodule update的话，就会提示你本地的subModule过时了。

执行一次git submodule update即可。

如果你是第一次拉submodule，则先执行git submodule init,然后执行git submodule update。


submodule的高危操作

10.1号 

master:

app
vhall-core : hash aabbccdd_1v

从master分支上 fork 一个分支 develop 分支进行开发.
vhall-core 这个大文件夹被视作一个外层git库中的一个普通文件. 受外层git库的版本控制. 
也就是说 master分支中 vhall-core 的hash值 与 test 分支中的 vhall-core hash 值会随外层版本变化.而产生不一致.


10.3 

有人修改了 app master 分支 vhall-core ,其最新hash为 ：  hash aabbccdd_2v.
如果你此时checkout到test分支. git status 会发现
    
     git status ....
     modified:   vhall-core (new commits)

这是因为 master分支的 vhall-core hash 是最新的。 git checkout test 时 . test分支会检测到 hash 值产生变化.
测试分支的hash值是最新的.是与master分支相同的. 但是是与test分支版本记录里面的hash是不同的.

如果此时 在test分支执行 git submodule update . 相当于 执行revert vhall-core的hash. 
回退到test分支中认为最新的hash.`aabbccdd_1v`。而丢弃了相对于master分支最新的hash`aabbccdd_2v.`


