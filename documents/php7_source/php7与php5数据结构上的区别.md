## php7与php5数据结构上的区别

从PHP7开始, 对于在zval的value字段中能保存下的值, 就不再对他们进行引用计数了, 而是在拷贝的时候直接赋值, 这样就省掉了大量的引用计数相关的操作, 这部分类型有:

    IS_LONG
    IS_DOUBLE
    
对于那种根本没有值, 只有类型的类型, 也不需要引用计数了:

    IS_NULL
    IS_FALSE
    IS_TRUE
    
而对于复杂类型, 一个`size_t`保存不下的, 那么我们就用`value`来保存一个指针, 这个指针指向这个具体的值, **引用计数也随之作用于这个值上, 而不在是作用于zval上了**.

综上所述,主要有一下几点改变:

 1. zval结构体字段的改变,一个zval的占用空间下降到16字节.
 1. hashtable 整块的数组元素和hash映射表全部连接在一起，被分配在同一块内存内,避免了CPU的缓存命中率下降问题，而php5中的bucket存储,并不是在内存中连续分配,而是分散在各个不同的内存区域.
PHP5的Hashtable对于每一个Bucket都是分开申请释放的,而存储在Hashtable中的数据是也会通过pListNext指针串成一个list，可以直接遍历 
 1. zval的类型做了比较大的调整, 扩充拆分到17种类型:
 1. php5中引用计数发生在zval上,php7中引用计数发生在`zval_value`上. 
 1. 增加了AST
 
#### php5 ZVAL

    struct _zval_struct {
         union {
              long lval;
              double dval;
              struct {
                   char *val;
                   int len;
              } str;
              HashTable *ht;
              zend_object_value obj;   // union中的最长字段 占用16个字节
              zend_ast *ast;           
         } value;
         zend_uint refcount__gc;           //  变量的引用计数 
         zend_uchar type;                  // 变量类型
         zend_uchar is_ref__gc;            // 表示了PHP中的一个类型是否是引用
    }; 

 - 根据`zend_uchar type;`字段的值不同, 我们就要用不同的方式解读`value`的值, `value`是个联合体. 
 比如对于type是IS_STRING, 那么我们应该用value.str来解读zval.value字段, 
 如果type是IS_LONG, 那么我们就要用value.lval来解读. 
 - 正常申请一个`zval`需要分配`24`个字节,如果算上垃圾回收时`zval_gc_info`对zval的扩充,真正的分配值为`32`个字节, 
 但其实GC只需要关心`IS_ARRAY`和`IS_OBJECT`类型(只有这两个类型会产生循环引用,其它类型使用简单的引用计数就可以解决), 这样就导致了大量的内存浪费.
 - PHP的zval大部分都是`按值传递, 写时拷贝`的值,(默认情况下数组也是按值传递,写时复制) 但是有俩例外, 就是`对象`和`资源`, 他们永远都是按引用传递.
 对象和资源在除了zval中的引用计数以外, 还需要一个全局的引用计数, 这样才能保证内存可以回收.所以在PHP5的时代, 以对象为例, 它有俩套引用计数, 一个是zval中的, 另外一个是obj自身的计数:
 
一个经典的引用问题:当我们调用`dummy`的时候, 本来只是简单的一个传值就行的地方, 但是因为`$array`曾经引用赋值给了`$b`, 所以导致`$array`变成了一个引用,
于是此处就会发生分离, 导致数组复制, 从而极大的拖慢性能.
  
    <?php
        function dummy($array) {}
        $array = range(1, 100000);
        $b = &$array;
        dummy($array);
    ?>


#### php7 ZVAL
    
    struct _zval_struct {
        zend_value        value;			/* value */
        union {
            struct {
                ZEND_ENDIAN_LOHI_4(             // 这个宏的作用是简化赋值, 它会保证在大端或者小端的机器上, 它定义的字段都按照一样顺序排列存储,
                    zend_uchar    type,			/* active type */
                    zend_uchar    type_flags,
                    zend_uchar    const_flags,
                    zend_uchar    reserved)	    /* call info for EX(This) */
            } v;
            uint32_t type_info;
        } u1;                                   // 根据这个联合体 我们可以知道 1. 该zval是否可以引用，2. zval_value 的类型.
        union {
            uint32_t     next;                 /* hash collision chain */
            uint32_t     cache_slot;           /* literal cache slot */
            uint32_t     lineno;               /* line number (for ast nodes) */
            uint32_t     num_args;             /* arguments number for EX(This) */
            uint32_t     fe_pos;               /* foreach position */
            uint32_t     fe_iter_idx;          /* foreach iterator index */
            uint32_t     access_flags;         /* class constant access flags */
            uint32_t     property_guard;       /* single property guard */
        } u2;                                   // 这个联合体主要用于一些特殊情况下进行字段扩展 比如hash冲突之类的.
    } zval ;
    
`php7`的`zval`按照8字节对齐方式存储,共占用`16`个字节.

      struct _zval_struct {
              zend_value        value;	// value字段 一个zend_value结构体 存储真实的变量值  占据8个字节 
              union  u1;         // 扩充字段 U1 占用4 字节
              union  u2;         // 扩充字段 U2 占用4 字节 
          };  
          
看一下`zend_value`结构,除了`整型`和`浮点型`直接在`zend_value`存储值,其它类型都是存储的指向值的指针.

    typedef union _zend_value {
    	zend_long         lval;				// long value 
    	double            dval;				// double value 
    	zend_refcounted  *counted;          // 从zend_value可以看出，除long、double类型直接存储值外，其它类型都为指针，指向各自的结构。
    	zend_string      *str;              // 指向一个zend字符串
    	zend_array       *arr;              // 指向一个zend_array
    	zend_object      *obj;              // 指向一个zend_object 
    	zend_resource    *res;              // ...
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

看一下`zend_array`的结构

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

       
**虽然在php层变量是弱类型,但是在底层变量是有明确的类型的.为什么变量需要有明确的类型呢？ 因为我们可以根据变量的类型知道变量的长度也就是变量占用的空间.**

看一下资源,对象结构

    struct _zend_object {
        zend_refcounted_h gc;
        uint32_t          handle;
        zend_class_entry *ce;                   //对象对应的class类
        const zend_object_handlers *handlers;
        HashTable        *properties;           //对象属性哈希表
        zval              properties_table[1];
    };
    
    struct _zend_resource {
        zend_refcounted_h gc;
        int               handle;
        int               type;
        void             *ptr;
    };
    
对象比较常见，资源指的是`tcp连接`、`文件句柄`等等类型，这种类型比较灵活，可以随意定义struct，通过ptr指向


#### 参考资料

https://www.laruence.com/2020/02/25/3182.html