## php的数组

散列表是根据关键码值(Key value)而直接进行访问的数据结构，它的key - value之间存在一个映射函数，可以根据key通过映射函数直接索引到对应的value值.
**它不以关键字的比较为基本操作，采用直接寻址技术**（就是说，它是直接通过key映射到内存地址上去的），从而加快查找速度，
在理想情况下，无须任何比较就可以找到待查关键字，查找的期望时间为`O(1)`.
可以理解为对关键字进行四则运算就可以得出数据记录的存储位置.


 - 数据结构
 - 映射函数 zend_array的实现
 - hash冲突
 - 数组的扩容 rehash

zend_array的结构

    typedef struct _zend_array HashTable;  // 看见没 zend_array 就是一个HashTable
    struct _zend_array {
        zend_refcounted_h gc;
        union {
            struct {
                ZEND_ENDIAN_LOHI_4(
                    zend_uchar    flags,
                    zend_uchar    nApplyCount,
                    zend_uchar    nIteratorsCount,
                    zend_uchar    consistency)
            } v;
            uint32_t flags;
        } u;
        uint32_t          nTableMask;          //  计算bucket索引时的掩码
        Bucket           *arData;              //  bucket数组
        uint32_t          nNumUsed;            //  已用bucket数
        uint32_t          nNumOfElements;      //  已有元素数，nNumOfElements <= nNumUsed，因为删除的并不是直接从arData中移除,而是标记为删除.
        uint32_t          nTableSize;          //  当前zend_array申请的内存空间  数组的大小，为2^n,必须为2的N次方 这样可以确保 hash计算数组下标的公式成立. 
        uint32_t          nInternalPointer;    //  数值游标  foreach 时使用  
        zend_long         nNextFreeElement;    //  
        dtor_func_t       pDestructor;         // 
    };

注意事项:

`arData[nTableSize]`数组存储实际的数据元素,也就是`Bucket`.Bucket的结构如下

    typedef struct _Bucket {
        zval              val;
        zend_ulong        h;                // 缓存 经过hash函数映射的 hash值
        zend_string      *key;              // 记录原始key
    } Bucket;

当把数组作为实参进行传递时,默认是按值传递的.传递了一个`zval`.相当在函数内部生成了一个新的`zval`副本.但是zval里面的`zend_array`是引用数+1,遵循`按值传递,写时复制`.

现在描述一下存储/索引数组元素的内部实现过程.

1 php中声明一个空数组 `$a = []`,php内核中声明一个zval变量,按顺序 zval.value 是一个指向 `zend_array`的指针.

2 初始化一个`zend_array`结构变量,根据`zend_array`的结构模板.会初始化一个如下的初始数组

     struct _zend_array {
             zend_refcounted_h gc;
             union {
                 struct {
                     ZEND_ENDIAN_LOHI_4(
                         zend_uchar    flags,
                         zend_uchar    nApplyCount,
                         zend_uchar    nIteratorsCount,
                         zend_uchar    consistency)
                 } v;
                 uint32_t flags;
             } u;
             uint32_t          nTableMask;          //  -8
             Bucket           *arData;              //  一个可以容纳8个Bucket的C数组.
             uint32_t          nNumUsed;            //  0
             uint32_t          nNumOfElements;      //  0
             uint32_t          nTableSize;          //  8 
             uint32_t          nInternalPointer;    //    
             zend_long         nNextFreeElement;    //  
             dtor_func_t       pDestructor;         // 
         };
         
分配arData空间 

    //新分配arData空间，大小为:(sizeof(Bucket) + sizeof(uint32_t)) * nSize
    new_data = pemalloc(HT_SIZE_EX(nSize, -nSize), ...);
    ht->nTableSize = nSize;
    ht->nTableMask = -ht->nTableSize;
    //将arData指针偏移到Bucket数组起始位置
    HT_SET_DATA_ADDR(ht, new_data);     
    
由上述代码可以知道,`arData[nTableSize]`仅仅为一个普通的C数组,并不是hash数组.我们在申请内存空间时,首先申请了一大块.
然后移动`*arData`到中间的某个位置，从这个位置为C数组的基准地址,然后顺序存储插入的Bucket.此时`*arData`指向的地址并不是我们刚才申请的整块内存的起始位置.
从我们刚才`申请的内存块的起始位置` 到 `*arData`位置,这一段内存空间存储的是我们需要的`散列表`.然后从`*arData`位置到整块内存的结束位置,存储的是Bucket.

 - 插入一个元素时先将元素按先后顺序插入Bucket数组，位置是`idx`，再根据key的哈希值映射到`散列表`中的某个位置`nIndex`，将`idx`存入这个位置；
 - 查找时先在根据key在散列表中映射到`nIndex`，得到`value`在`Bucket`数组的位置`idx`，再从`Bucket`数组中取出元素.

#### 参考资料


>PHP数组的变化（HashTable和Zend Array）
在编写PHP程序过程中，使用最频繁的类型莫过于数组，PHP5的数组采用HashTable实现。
如果用比较粗略的概括方式来说，它算是一个支持双向链表的HashTable，
不仅支持通过数组的key来做hash映射访问元素，
也能通过foreach以访问双向链表的方式遍历数组元素。
PHP5的HashTable：
这个图看起来很复杂，各种指针跳来跳去，当我们通过key值访问一个元素内容的时候，有时需要3次的指针跳跃才能找对需要的内容。
而最重要的一点，**就在于这些数组元素存储,并不是在内存中连续分配,而是分散在各个不同的内存区域.
同理可得，在CPU读取的时候，因为它们就很可能不在同一级缓存中，会导致CPU不得不到下级缓存甚至内存区域查找，也就是引起CPU缓存命中下降，进而增加更多的耗时。**
新版本的数组结构，非常简洁，让人眼前一亮。最大的特点是，整块的数组元素和hash映射表全部连接在一起，被分配在同一块内存内。
如果是遍历一个整型的简单类型数组，效率会非常快，因为，数组元素（Bucket）本身是连续分配在同一块内存里，
并且，数组元素的zval会把整型元素存储在内部，也不再有`指针外链`，全部数据都存储在当前内存区域内。
当然，最重要的是，它能够避免CPU Cache Miss（CPU缓存命中率下降）。    

https://www.laruence.com/2020/02/25/3182.html

