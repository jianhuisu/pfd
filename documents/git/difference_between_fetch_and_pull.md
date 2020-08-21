## difference between git fetch and git pull

Git中从远程的分支获取最新的版本到本地有这样2个命令：


 - git fetch 从远程获取最新版本到本地，不会自动merge
 - git pull  从远程获取最新版本到本地 自动merge 

####  git fetch

    git fetch origin master
    git log -p master origin/master
    git merge origin/master

以上命令的含义：

 - 首先从远程的`origin`的`master`主分支下载最新的版本到local`origin/master`分支上
 - 比较本地的`master`分支和`origin/master`分支的差别
 - 最后进行合并
 
上述过程其实可以用以下更清晰的方式来进行：
 
 - git fetch origin master:tmp
 - git diff tmp 
 - git merge tmp

从远程获取最新的版本到本地的test分支上之后再进行比较合并.

eg.1

    sujianhui@dev529> git fetch
    remote: Enumerating objects: 5, done.
    remote: Counting objects: 100% (5/5), done.
    remote: Compressing objects: 100% (2/2), done.
    remote: Total 3 (delta 2), reused 1 (delta 1), pack-reused 0
    Unpacking objects: 100% (3/3), done.
    From https://github.com/jianhuisu/leetcode
       ed5e90c..d223030  master     -> origin/master
    
    sujianhui@dev529> git log -p
    commit ed5e90c53699714c9dec9732b71ad8f0659f2275
    Merge: 4c66b7f 32e5850
    Author: sujianhui <1051034413@qq.com>
    Date:   Sat Aug 22 00:03:22 2020 +0800
    
        Merge branch 'master' of https://github.com/jianhuisu/leetcode
    
    ......
    
    diff --git a/dynamic_programing/fibonacci.c b/dynamic_programing/fibonacci.c
    new file mode 100644
    index 0000000..6132d6e
    --- /dev/null
    +++ b/dynamic_programing/fibonacci.c
    @@ -0,0 +1,32 @@
    +#include <stdio.h>
    
    sujianhui@dev529> git merge
    fatal: No commit specified and merge.defaultToUpstream not set.
    
    sujianhui@dev529> git merge origin/master
    Updating ed5e90c..d223030
    Fast-forward
     README.md | 1 -
     1 file changed, 1 deletion(-)
     
    
#### git pull

从远程获取最新版本并merge到本地.`git pull origin master` 或者 `git pull` (默认情况下是拉取与本地分支 mapping 远程分支)

上述命令其实相当于`git fetch` + `git merge`.

在实际使用中，git fetch更安全一些,因为在merge前，我们可以查看更新情况，然后再决定是否合并。