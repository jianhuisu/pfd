# array_map

对数组中每个元素应用回调

    array_map(function ($evPart) use (&$controller){
        $controller .= ucfirst($evPart);
    },$originControllerName);
    
    
一个老生常谈的问题  array_map 与 foreach 的区别.

如果仅仅考虑遍历数组元素这个功能上.两者基本没区别.除了foreach在性能上碾压array_map. (毕竟一个是语言关键字，另外一个是函数涉及到上下文切换)

但是如果从功能上展开比较. 那么array_map要比foreach更省事一点.

！！！回调函数接受的参数数目应该和传递给 array_map() 函数的数组数目一致。
    
    <?php
        function myfunction($v1,$v2)
        {
            if ($v1===$v2)
              {
              return "same";
              }
            return "different";
        }
        
        $a1=array("Horse","Dog","Cat");
        $a2=array("Cow","Dog","Rat");
        print_r(array_map("myfunction",$a1,$a2));
    ?>

运行一下试试.
    
    <?php
    function myfunction($v)
    {
    $v=strtoupper($v);
      return $v;
    }
    
    $a=array("Animal" => "horse", "Type" => "mammal");
    print_r(array_map("myfunction",$a));
    ?>
    
    
array_map 与 array_walk 稍有区别. array_walk在回调时可以接收 key.

      <?php
          function myfunction($value,$key)
          {
          echo "The key $key has the value $value<br>";
          }
          $a=array("a"=>"red","b"=>"green","c"=>"blue");
          array_walk($a,"myfunction");
      ?> 