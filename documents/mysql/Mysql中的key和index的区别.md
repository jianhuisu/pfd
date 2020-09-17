
    mysql> create table user( 
        id int unsigned not null auto_increment primary key,
        name char(50) not null default '',
        depart_id int unsigned not null default 0,
        join_time datetime not null,
        index name(name),
        key `un` (`name`,`depart_id`)
    )engine=innodb charset=utf8mb4;
    Query OK, 0 rows affected (0.15 sec)

show create table 

    CREATE TABLE `user` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `name` char(50) NOT NULL DEFAULT '',
      `depart_id` int unsigned NOT NULL DEFAULT '0',
      `join_time` datetime NOT NULL,
      PRIMARY KEY (`id`),
      KEY `name` (`name`),
      KEY `un` (`name`,`depart_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
    
    
show index from user

    mysql> show index from user\G
    *************************** 1. row ***************************
            Table: user
       Non_unique: 0
         Key_name: PRIMARY
     Seq_in_index: 1
      Column_name: id
        Collation: A
      Cardinality: 0
         Sub_part: NULL
           Packed: NULL
             Null: 
       Index_type: BTREE
          Comment: 
    Index_comment: 
          Visible: YES
       Expression: NULL
    *************************** 2. row ***************************
            Table: user
       Non_unique: 1
         Key_name: name
     Seq_in_index: 1
      Column_name: name
        Collation: A
      Cardinality: 0
         Sub_part: NULL
           Packed: NULL
             Null: 
       Index_type: BTREE
          Comment: 
    Index_comment: 
          Visible: YES
       Expression: NULL
    *************************** 3. row ***************************
            Table: user
       Non_unique: 1
         Key_name: un
     Seq_in_index: 1
      Column_name: name
        Collation: A
      Cardinality: 0
         Sub_part: NULL
           Packed: NULL
             Null: 
       Index_type: BTREE
          Comment: 
    Index_comment: 
          Visible: YES
       Expression: NULL
    *************************** 4. row ***************************
            Table: user
       Non_unique: 1
         Key_name: un
     Seq_in_index: 2
      Column_name: depart_id
        Collation: A
      Cardinality: 0
         Sub_part: NULL
           Packed: NULL
             Null: 
       Index_type: BTREE
          Comment: 
    Index_comment: 
          Visible: YES
       Expression: NULL
    4 rows in set (0.01 sec)


https://blog.csdn.net/liangwenmail/article/details/86703646
    
    