## 查询

### 关联查询

交叉连接（CROSS JOIN）
内连接（INNER JOIN）
外连接（LEFT JOIN/RIGHT JOIN）
联合查询（UNION与UNION ALL）
全连接（FULL JOIN）   MySQL不支持全连接
交叉连接（CROSS JOIN）

### 子查询

    select  * from employee where salary=(select max(salary) from employee);
   
### mysql的慢查询问题 

其实通过慢查询日志来分析是一种比较简单的方式，如果不想看日志，可以借助工具来完成，
如`mysqldumpslow`, `mysqlsla`, `myprofi`, `mysql-explain-slow-log`, `mysqllogfilter`等，感觉自己来分析一个需要丰富的经验，一个浪费时间。
      