## grep 

Globally search a Regular Expression and Print的缩写，意思是：全局搜索一个正则表达式，并且打印
grep命令的功能简单说来是在文件中查找关键字，并且显示关键字所在的行。

命令格式：
grep 【options】 file

grep "hellow world" 1.txt
如果要匹配的字符串中间包含空格，需要使用双引号将其包裹起来
grep hello 1.txt

grep -v “hello world” 1.txt
反选，显示不包含  hello world 的行
grep -i
忽略大小写
grep -n
显示行号
grep -r
在所有子目录和子文件中查找
grep的高级用法：配合正则表达式
grep -E pattern  1.txt
grep -E 200281.*roleId\"\:\"1 webSocketMessage.log

别名
egrep 200281.*roleId\"\:\"1 webSocketMessage.log