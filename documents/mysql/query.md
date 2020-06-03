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
      