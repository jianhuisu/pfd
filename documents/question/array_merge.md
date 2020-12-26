使用 + 合并两个索引数组. 保留索引关系. 相同位置 保留第一次出现的值.

    <?php
    
    $a = [0, 1, 2, 3];
    $b = [6, 2, 3, 4, 5];
    
    // $a + $b 按位置进行合并 而不是元素的值
    // = [0, 1, 2, 3, 5];

使用 + 合并两个关联数组.保留关联关系. 相同位置 保留第一次出现的值.

    <?php
    
    $a = ['name1' => 'sjh1', 'name2' => 'sjh2', 'name3' => 'sjh3'];
    $b = ['name1' => 'sjh1111', 'name2' => 'sjh22222', 'name3' => 'sjh3333'];
    
    // $a + $b 按位置进行合并 而不是元素的值
    array(3) {
        ["name1"]=>
      string(4) "sjh1"
        ["name2"]=>
      string(4) "sjh2"
        ["name3"]=>
      string(4) "sjh3"
    }

使用 array_merge 合并关联数组, 保留最后一次出现的值.

    <?php
    
    $a = ['name1' => 'sjh1', 'name2' => 'sjh2', 'name3' => 'sjh3'];
    $b = ['name1' => 'sjh1111', 'name2' => 'sjh22222', 'name3' => 'sjh3333'];
    
    var_dump(array_merge($a,$b));
    
    array(3) {
      ["name1"]=>
      string(7) "sjh1111"
      ["name2"]=>
      string(8) "sjh22222"
      ["name3"]=>
      string(7) "sjh3333"
    }

使用 array_merge 合并索引数组 . 忽略键值 直接进行合并. 结果数组中可能会出现重复值

    <?php
    
    $a = [0, 1, 2, 3];
    $b = [6, 2, 3, 4, 5];
    
    var_dump(array_merge($a,$b));

    array(9) {
      [0]=>
      int(0)
      [1]=>
      int(1)
      [2]=>
      int(2)
      [3]=>
      int(3)
      [4]=>
      int(6)
      [5]=>
      int(2)
      [6]=>
      int(3)
      [7]=>
      int(4)
      [8]=>
      int(5)
    }
