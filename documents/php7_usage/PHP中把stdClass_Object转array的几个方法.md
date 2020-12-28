PHP和JS通讯通常都用json，但用 json 传过来的数组并不是标准的array，而是 stdClass 类型。那么我们可以参考下面的几个方法进行转换。
方法一：

复制代码代码如下:

//PHP stdClass Object转array 
function object_array($array) { 
    if(is_object($array)) { 
        $array = (array)$array; 
     } if(is_array($array)) { 
         foreach($array as $key=>$value) { 
             $array[$key] = object_array($value); 
             } 
     } 
     return $array; 
}
方法二：

复制代码代码如下:

$array = json_decode(json_encode(simplexml_load_string($xmlString)),TRUE);


方法三：


复制代码代码如下:

 function object2array_pre(&$object) {
        if (is_object($object)) {
            $arr = (array)($object);
        } else {
            $arr = &$object;
        }
        if (is_array($arr)) {
            foreach($arr as $varName => $varValue){
                $arr[$varName] = $this->object2array($varValue);
            }
        }
        return $arr;
    }

如果是10W的数据量的话，执行要进1s，结构再复杂些，可以达到3s， 性能太差了
可以用以下替换：
 
 看这里  ------------ 看这里
 
复制代码代码如下:
function object2array(&$object) {
             $object =  json_decode( json_encode( $object),true);
             return  $object;
    }

 但是对json的特性，只能是针对utf8的，否则得先转码下。