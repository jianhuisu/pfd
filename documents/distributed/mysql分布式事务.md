# Mysql分布式事务及优缺点

在分布式环境下，事务的提交会变得相对比较复杂，因为多个节点的存在，可能会存在部分节点提交失败的情况，只有所有节点均成功提交 整个分布式事务才算完成，即分布式事务的ACID特性需要在各个节点的数据库实例同时得到保证。

总而言之，在分布式提交时，只要发生一个节点提交失败，则所有的节点都不能提交，只有当所有节点都能提交时，整个分布式事务才允许被提交。

mysql好像是从5.0开始支持分布式事务

这里先声明两个概念：

  - `资源管理器（resource manager）`
  - `事务管理器（transaction manager）`

##### `资源管理器（resource manager）`

管理系统资源,数据库就是一种资源管理器。资源管理还应该具有管理事务提交或回滚的能力。（其实就是分布式事务中的一个事务节点）

##### `事务管理器`

事务管理器是分布式事务的核心管理者。事务管理器与每个资源管理器（resource manager）进行通信，协调并完成事务的处理。
事务的各个分支由唯一命名进行标识。mysql在执行分布式事务（外部XA）的时候，mysql服务器相当于xa事务资源管理器，与mysql链接的客户端相当于事务管理器。



例如: php服务A中连接mysql_A. 其中要同步操作其它四个mysql节点的数据.

eg.1

	<?php

	DB::beginTransaction();
	try {

	    file_get_contents($node_1);
	    file_get_contents($node_2);
	    file_get_contents($node_3);
	    file_get_contents($node_4);

	    DB::commit();

	} catch (\Exception $e) {
	    DB::rollback();
	}

这就是一个典型的分布式事务场景.(当然命令的使用不对)

因为XA 事务是基于两阶段提交协议的，**所以需要有一个事务协调者（transaction manager）来保证所有的事务参与者都完成了准备工作(第一阶段)。**
如果事务协调者（transaction manager）收到所有参与者都准备好的消息，就会通知所有的事务都可以提交了（第二阶段）。
MySQL在这个XA事务中扮演的是参与者的角色，而不是事务协调者（transaction manager）。


注意:Mysql的XA事务分为外部XA和内部XA

外部XA用于跨多MySQL实例的分布式事务，需要应用层作为协调者，通俗的说就是比如我们在PHP中写代码，那么PHP书写的逻辑就是协调者。（典型应用场景微服务）
应用层负责决定提交还是回滚，崩溃时的悬挂事务。MySQL数据库外部XA可以用在分布式数据库代理层，实现对MySQL数据库的分布式事务支持，例如开源的代理工具：网易的DDB，淘宝的TDDL等等。

`内部XA事务用于同一实例下跨多引擎事务`，由Binlog作为协调者，比如在一个存储引擎提交时，需要将提交信息写入二进制日志，
这就是一个分布式内部XA事务，只不过二进制日志的参与者是MySQL本身。Binlog作为内部XA的协调者，在binlog中出现的内部xid，
在crash recover时，由binlog负责提交。(这是因为，binlog不进行prepare，只进行commit，因此在binlog中出现的内部xid，一定能够保证其在底层各存储引擎中已经完成prepare)。


### 分布式事务原理：分段式提交

分布式事务通常采用2PC协议，全称`Two Phase Commitment Protocol`。该协议主要为了解决在分布式数据库场景下，所有节点间数据一致性的问题。分布式事务通过2PC协议将提交分成两个阶段：

 1. `prepare`
 1. `commit/rollback`

解释:

 - 阶段一: 准备（prepare）阶段。即所有的参与者准备执行事务并锁住需要的资源。参与者ready时，向transaction manager报告已准备就绪。
 - 阶段二: 提交阶段（commit）。当transaction manager确认所有参与者都ready后，向所有参与者发送commit命令。


### `mysql xa`事务的语法

1、首先要确保mysql开启XA事务支持

	SHOW VARIABLES LIKE '%xa%'

如果innodb_support_xa的值是ON就说明mysql已经开启对XA事务的支持了。 

如果不是就执行：

	SET innodb_support_xa = ON

主要有：

 - SHOW VARIABLES LIKE '%xa%'     // 如果innodb_support_xa的值是ON就说明mysql已经开启对XA事务的支持了。 
 - XA START 'any_unique_id';      //  'any_unique_id' 是用户给的，全局唯一在一台mysql中开启一个XA事务
 - XA END 'any_unique_id ';       //  标识XA事务的操作结束
 - XA PREPARE 'any_unique_id';    //  告知mysql 准备提交这个xa事务
 - XA COMMIT 'any_unique_id';     //  告知mysql提交这个xa事务
 - XA ROLLBACK 'any_unique_id';   //  告知mysql回滚这个xa事务
 - XA RECOVER;                    //查看本机mysql目前有哪些xa事务处于prepare状态

XA事务恢复

如果执行分布式事务的mysql crash了，mysql 按照如下逻辑进行恢复：

 - 如果这个xa事务commit了，那么什么也不用做
 - 如果这个xa事务还没有prepare，那么直接回滚它
 - 如果这个xa事务prepare了，还没commit， 那么把它恢复到prepare的状态，由用户去决定commit或rollback

当mysql crash后重新启动之后，执行“XA RECOVER；”查看当前处于prepare状态的xa事务，然后commit或rollback它们。

`使用限制`

1. XA事务和本地事务以及锁表操作是互斥的,开启了xa事务就无法使用本地事务和锁表操作

	mysql> xa start 't1xa';
	Query OK, 0 rows affected (0.04 sec)
	mysql> begin;
	ERROR 1399 (XAE07): XAER_RMFAIL: The command cannot be executed when global transaction is in the ACTIVE state
	mysql> lock table t1 read;
	ERROR 1399 (XAE07): XAER_RMFAIL: The command cannot be executed when global transaction is in the ACTIVE state

开启了本地事务就无法使用xa事务

	mysql> begin;
	Query OK, 0 rows affected (0.00 sec)
	mysql> xa start 'rrrr';
	ERROR 1400 (XAE09): XAER_OUTSIDE: Some work is done outside global transaction


2. xa start 之后必须xa end， 否则不能执行xa commit 和xa rollback. 所以如果在执行xa事务过程中有语句出错了，你也需要先xa end一下，然后才能xarollback。

3. 效率低下，准备阶段的成本持久，全局事务状态的成本持久，性能与本地事务相差10倍左右； 提交前，出现故障难以恢复和隔离问题。

#### 注意事项

 1. mysql只是提供了xa事务的接口，分布式事务中的mysql实例之间是互相独立的不感知的。 所以用户必须自己实现分布式事务的调度器
 
xa事务有一些使用上的bug， 参考http://www.mysqlops.com/2012/02/24/mysql-xa-optimize.html

主要是

“MySQL数据库的主备数据库的同步，通过Binlog的复制完成。而Binlog是MySQL数据库内部XA事务的协调者，
并且MySQL数据库为binlog做了优化——binlog不写prepare日志，只写commit日志。
所有的参与节点prepare完成，在进行xa commit前crash。crash recover如果选择commit此事务。由于binlog在prepare阶段未写，因此主库中看来，此分布式事务最终提交了，但是此事务的操作并未 写到binlog中，因此也就未能成功复制到备库，从而导致主备库数据不一致的情况出现。
而crash recover如果选rollback, 那么就会出现全局不一致（该分布式事务对应的节点，部分已经提交，无法回滚，而部分节点回滚。最终导致同一分布式事务，在各参与节点，最终状态不一致）”

参考的那篇blog中给出的办法是修改mysql代码，这个无法在DBScale中使用。 所以可选的替代方案是不使用主从复制进行备份，而是直接使用xa事务实现同步写来作为备份。

### php yii 分布式事务实现实例

db_finance库下

	CREATE TABLE `t_user_account` (
	  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
	  `username` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名',
	  `money` int(11) NOT NULL DEFAULT '0' COMMENT '账户金额',
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

db_order库下

	CREATE TABLE `t_user_orders` (
	  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
	  `username` varchar(255) NOT NULL DEFAULT '',
	  `money` int(11) NOT NULL DEFAULT '0' COMMENT '订单扣款金额',
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;


php代码


	$username = '温柔的风';
	$order_money = 100;

	$addOrder_success = addOrder($username,$order_money);
	$upAccount_success = updateAccount($username,$order_money);


	if($addOrder_success['state'] =="yes" && $upAccount_success['state']=="yes"){
	   commitdb($addOrder_success['xa']);
	   commitdb1($upAccount_success['xa']);
	}else{
	   rollbackdb($addOrder_success['xa']);
	   rollbackdb1($upAccount_success['xa']);
	}
	die;
	function addOrder ($username, $order_money){

	    $xa = uniqid("");

	    $sql_xa = "XA START '$xa'";
	    $db = Yii::app()->dborder_readonly;
	    $db->createCommand($sql_xa)->execute();

	    $insert_sql = "INSERT INTO t_user_orders (`username`,`money`) VALUES ($username,$order_money)";
	    $id = $db->createCommand($insert_sql)->execute();

	    $db->createCommand("XA END '$xa'")->execute();
	    if ($id) {

	        $db->createCommand("XA PREPARE '$xa'")->execute();
	        return ['state' => 'yes', 'xa' => $xa];
	    }else {
	        return ['state' => 'no', 'xa' => $xa];
	    }

	}

	function updateAccount($username, $order_money){
	    $xa = uniqid("");
	    $sql_xa = "XA START '$xa'";
	    $db = Yii::app()->db_finance;
	    $db->createCommand($sql_xa)->execute();

	    $sql = "update t_user_account set money=money-".$order_money." where username='$username'";


	    $id = $db->createCommand($sql)->execute();

	    $db->createCommand("XA END '$xa'")->execute();
	    if ($id) {
	        $db->createCommand("XA PREPARE '$xa'")->execute();
	        return ['state' => 'yes', 'xa' => $xa];
	    }else {
	        return ['state' => 'no', 'xa' => $xa];
	    }

	}


	//提交事务！
	function commitdb($xa){

	    $db = Yii::app()->dborder_readonly;
	    return $db->createCommand("XA COMMIT '$xa'")->execute();
	}

	//回滚事务
	function rollbackdb($xa){

	    $db = Yii::app()->dborder_readonly;
	    return $db->createCommand("XA COMMIT '$xa'")->execute();
	}

	//提交事务！
	function commitdb1($xa){

	    $db = Yii::app()->db_finance;
	    return $db->createCommand("XA COMMIT '$xa'")->execute();

	}
	//回滚事务
	function rollbackdb1($xa){
	    $db = Yii::app()->db_finance;
	    return $db->createCommand("XA ROLLBACK '$xa'")->execute();


	}

### 参考资料 

https://www.cnblogs.com/wt645631686/p/10882998.html