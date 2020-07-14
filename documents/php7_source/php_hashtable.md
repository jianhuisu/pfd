## php的数组的底层实现 hashtable

映射函数(即：散列函数)是散列表的关键部分，它将key与value建立映射关系，一般映射函数可以根据key的哈希值与Bucket数组大小取模得到，即key->h % ht->nTableSize，但是PHP却不是这么做的：

    nIndex = key->h | ht->nTableMask;
    
显然位运算要比取模更快。

`nTableMask`为`nTableSize`的负数，即:`nTableMask = -nTableSize`，因为`nTableSize`等于`2^n`，所以`nTableMask`二进制位右侧全部为`0`，
也就保证了`nIndex`落在数组索引的范围之内(`|nIndex| <= nTableSize`)：

    11111111 11111111 11111111 11111000   -8
    11111111 11111111 11111111 11110000   -16
    11111111 11111111 11111111 11100000   -32
    11111111 11111111 11111111 11000000   -64
    11111111 11111111 11111111 10000000   -128


dddddddddd

 - packet array  php中的索引数组
 - hash array    php中的关联数组


    //Bucket：散列表中存储的元素
    typedef struct _Bucket {
        zval              val; //  存储的是 key:value 中的value，这里嵌入了一个zval，而不是一个指针
        zend_ulong        h;   // 通过哈希算法得出的哈希值.  key根据times 33计算得到的哈希值，或者是数值索引编号
        zend_string      *key; // 存储的是 key:value 中的 key
    } Bucket;
    
    //HashTable结构
    typedef struct _zend_array HashTable;
    struct _zend_array {
        zend_refcounted_h gc;
        union {
            struct {
                ZEND_ENDIAN_LOHI_4(
                        zend_uchar    flags,
                        zend_uchar    nApplyCount,
                        zend_uchar    nIteratorsCount,
                        zend_uchar    reserve)
            } v;
            uint32_t flags;
        } u;
        uint32_t          nTableMask; // 哈希值计算掩码，等于nTableSize的负值(nTableMask = -nTableSize) ，用来计算元素落在哪个bucket中
        Bucket           *arData;     // 存储真正元素的C数组，数组中的每一个元素都是一个bucket,该指针指向第一个Bucket
        uint32_t          nNumUsed;   // 已用Bucket数   包括被unset()掉的值
        uint32_t          nNumOfElements; // 哈希表有效元素数 真正有意义的值 不包括被unset()掉的值   
        uint32_t          nTableSize;     // 哈希表总大小，为2的n次方
        uint32_t          nInternalPointer; // 内部指针
        zend_long         nNextFreeElement; // 下一个可用的数值索引,如:arr[] = 1;arr["a"] = 2;arr[] = 3;  则nNextFreeElement = 2;
        dtor_func_t       pDestructor;   // 析构指针
    };