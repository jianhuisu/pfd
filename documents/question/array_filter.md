# array_filter 

过滤掉数组中的空值. 返回非空元素的新集合. 保留索引关系.

    <?php
    
    $a = [
        '',
        'name1',
        0,
        'name2',
        [],
        'name3',
        false,
    ];
    
    var_dump(array_filter($a));


结果

    array(3) {
      [1]=>
      string(5) "name1"
      [3]=>
      string(5) "name2"
      [5]=>
      string(5) "name3"
    }

