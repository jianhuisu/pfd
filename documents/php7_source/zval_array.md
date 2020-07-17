## php数组的实现

php数组是由哈希表+链表实现，准确来说，是由哈希表+双向链表实现

哈希表，顾名思义，即将不同的关键字映射到不同单元的一种数据结构。
而将不同关键字映射到不同单元的方法就叫做哈希函数.
理想情况下，经过哈希函数处理，关键字和单元是会进行一一对应的；
但是如果关键字值足够多的情况下，就容易出现多个关键字映射到同一单元的情况，即出现哈希冲突.
哈希冲突的解决方案，

 - 链接法
 - 开放寻址法

#### php数组的分类

这部分是错误的理解，等待更新

 - `packed array`  `[1,2,3]` 类似于我们常说的索引数组.但是必须是按序的.`__packed`是`字节对齐`的意思.
 - `hash array`  `['name' =>  'age' => ,'gender' =>]`  类似于我们常说的关联数组. 

可以这么简单的理解：

 - 对于key是数字的，就不用涉及到hash运算，此时使用的是`packed array`； 当然如果`key`的值较大，
   或者间隔较大，还是会退化成`hash array`.`packed array`能够节省索引部分占用的内存,是一个性能上的优化.
    
 - 对于`key`是非数字的，必须用`hash`算法进行计算出来它所在`bucket`的位置，
   那么索引数组是必不可少的，只能是`hash array`
   

packed array可以认为类似于c语言中的数组， 数组的特点是“顺序”存储的，那么我们从0到20w是顺序的，所以可以一直保持“c语言数组”的特点，一直是packed array。
这时候请注意，是不需要额外的“索引”来记录每个元素的位置的，因为“c语言数组”的下标是天然的索引。
而当我们从20w倒着插入的时候，就不是“顺序”的了，这时候需要“索引”来记录每个元素的位置，比如第20w放在第0个位置，这就要一个索引进行对应的。
此时就是`hash array`。索引放在arData的前面的int32_t类型的数组中。
   

这是底层的实现，对于我们写php代码，需要关注的点是对于业务中的大数组，有没有可能设计一些算法，让它满足packed array的性质，这样可以节省内存； 另外一方面就是要关注在大数组的情况下，可能会发声packed array向 hash array的转变，这个耗时还是较大的，需要尽量避免这种情况。  当然这两种情况都是针对“大”数组，小数组的情况下，其实差距没那么大。

#### 数据结构解析

`nNumUsed`和`nNumOfElements`的区别： `nNumUsed`指的是`arData`数组中已使用的`Bucket`数，
因为数组在删除元素后只是将该元素`Bucket`对应值的类型设置为`IS_UNDEF`,（如果每次删除元素都要将数组移动并重新索引太浪费时间），
而`nNumOfElements`对应的是数组中真正的元素个数。
 
`nTableSize`数组的容量，该值为`2`的幂次方。PHP的数组是不定长度但C语言的数组定长的，
为了实现`PHP`的不定长数组的功能，采用了`扩容`的机制，
就是在每次插入元素的时候判断`nTableSize`是否足以储存。
如果不足则重新申请`2`倍`nTableSize`大小的新数组，并将原数组复制过来（此时清除原数组中类型为`IS_UNDEF`元素的时机）并重新索引。
`nTableSize`的默认值为`8`
 
##### 一个php数组在Zend中会占用多大空间. 

现在有一个php数组,求该数组占用空间

    <?php
    // php version : php 7.1.0
    $a = [1,2,3,4];
    
gdb 

    typedef struct _zval_struct     zval;
    struct _zval_struct {
        zend_value        value;		       // 存储变量数据的联合体
        union {
            struct {
                ZEND_ENDIAN_LOHI_4(
                    zend_uchar    type,	       // var real type 	
                    zend_uchar    type_flags,  // var is const ? reference ? callable ? 
                    zend_uchar    const_flags, // const 
                    zend_uchar    reserved)	   // 保留字段 
            } v;
            uint32_t type_info;
        } u1;
        union {
            uint32_t     next;                 /* hash collision chain */
            uint32_t     cache_slot;           /* literal cache slot */
            uint32_t     lineno;               /* line number (for ast nodes) */
            uint32_t     num_args;             /* arguments number for EX(This) */
            uint32_t     fe_pos;               /* foreach position */
            uint32_t     fe_iter_idx;          /* foreach iterator index */
            uint32_t     access_flags;         /* class constant access flags */
            uint32_t     property_guard;       /* single property guard */
        } u2;
    };

sizeof(zval) : 16 Bytes

    typedef union _zend_value {
    	zend_long         lval;				/* long value */
    	double            dval;				/* double value */
    	zend_refcounted  *counted;
    	zend_string      *str;
    	zend_array       *arr;
    	zend_object      *obj;
    	zend_resource    *res;
    	zend_reference   *ref;
    	zend_ast_ref     *ast;
    	zval             *zv;
    	void             *ptr;
    	zend_class_entry *ce;
    	zend_function    *func;
    	struct {
    		uint32_t w1;
    		uint32_t w2;
    	} ww;
    } zend_value;

sizeof(zend_value) : 8 Bytes 

对于一个数组来说,`zend_value`中只是存储了指向真实数据的一个指针,它还需要占用一个`zend_array`结构

    typedef struct _zend_array HashTable;
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
        uint32_t          nTableMask;       /*count index value */
        Bucket           *arData;
        uint32_t          nNumUsed;          /*已使用 Bucket 数 已经使用的空间*/
        uint32_t          nNumOfElements;    /* 真实的元素个数  has used elements , include skip position*/
        uint32_t          nTableSize;        /* 数组的容量，该值为 2 的幂次方。PHP 的数组是不定长度但 C 语言的数组定长的，为了实现 PHP 的不定长数组的功能，采用了「扩容」的机制，就是在每次插入元素的时候判断 nTableSize 是否足以储存。如果不足则重新申请 2 倍 nTableSize 大小的新数组，并将原数组复制过来（此时正是清除原数组中类型为 IS_UNDEF 元素的时机）并且重新索引。, 2x increment : 8 -> 16 -> 32 */
        uint32_t          nInternalPointer;  // 内部指针，用于遍历
        zend_long         nNextFreeElement;  /* define index array , find key value from this attribute $array[]= 12; */
        dtor_func_t       pDestructor;        // 析构函数
    };

sizeof(_zend_array) : 56 Bytes
sizeof(Bucket) : 32 Bytes

php数组中一共有四个整型元素,那么估算该数组在Zend中将会占用:
1个zval,1个zend_array,4个Bucket
    
    16 + 56 + 32 * 4 = 200 Bytes
    
使用php中的内存检测函数印证一下:
    
    <?php
    echo '开始内存：'.memory_get_usage(), ''; 
    $a = [1,2,3,4];
    echo '运行后内存：'.memory_get_usage(), '';  
    unset($a);   
    echo '回到正常内存：'.memory_get_usage()."\n"; 
    
    // result -> 64 Bytes
    
结果对不上.这是为什么呢？？？换一个整型试一下 

    <?php
    echo '开始内存：'.memory_get_usage(), ''; 
    $a = 1;
    echo '运行后内存：'.memory_get_usage(), '';  
    unset($a);   
    echo '回到正常内存：'.memory_get_usage()."\n";     
    // result -> 72 Bytes

按照数据结构估算应该占用24Bytes....但是怎么一个整型变量比数组变量占用空间还要大... 这是因为有对齐等操作.

##### 数组的增删改查

string 写时复制.
array 写时分离.



#### 参考资料

https://segmentfault.com/a/1190000018720188  
https://www.jianshu.com/p/5faa06af694f