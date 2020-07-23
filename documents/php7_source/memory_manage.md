## 内存管理

理论上`php-fpm`每个worker进程都可以向申请`ulimit -m`KB的内存空间.但是上来就直接申请几个G的内存空间属实有点浪费.
这将引起不必要的swap操作.所以大部分进程的处理方式为:

worker首先向操作系统申请一定量的内存.如果需要分配小块内存,那么在申请的内存空间内分配即可.
如果在运行过程中发现内存空间不够用,那么重新向操作系统申请一大块内存.这样做的目的是尽量减少向操作系统申请内存操作.
php内核向操作系统申请内存时并不是调用的`malloc()`,`malloc()`是glibc实现的内存操作，并不是操作系统提供的接口.Linux提供的接口为`mmap()`.
zend自己实现了一套内存管理接口,其实就是`mmap()`外边包装了一层.来支持zend内存管理机制的一些特性.

 - `emalloc()` 提供对Hugepage的支持 
 - `efree()`
 - `estrdup()`

内存池是内核中最底层的内存操作，定义了三种粒度的内存块：

 - chunk 2M 一个chunk切割为512个page , 第一个page自用，其余511个page公用
 - page  4KB
 - slot  内存池提前定义好了30种规格 (8,16,24,32,48...3072)bytes不等，申请时在对应的page上的头slot中查找可用位置.

申请内存时按照不同的申请大小决定具体的分配策略：

 - Huge(chunk): 申请内存大于2M，直接调用系统分配，分配若干个chunk
 - Large(page): 申请内存大于3092B(3/4 page_size)，小于2044KB(511 page_size)，分配若干个page
 - Small(slot): 申请内存小于等于3092B(3/4 page_size)

#### 内存池初始化

内存池在php_module_startup阶段初始化，`start_memory_manager()`：
`alloc_globals `是一个全局变量，即 AG宏 ，它只有一个成员:mm_heap，保存着整个内存池的信息，所有内存的分配都是基于这个值，多线程模式下(ZTS)会有多个`heap`
也就是说每个线程都有一个独立的内存池

	static zend_mm_heap *zend_mm_init(void)
	{
	    //向系统申请2M大小的chunk
	    zend_mm_chunk *chunk = (zend_mm_chunk*)zend_mm_chunk_alloc_int(ZEND_MM_CHUNK_SIZE, ZEND_MM_CHUNK_SIZE);
	    zend_mm_heap *heap;

	    heap = &chunk->heap_slot; //heap结构实际是主chunk嵌入的一个结构，后面再分配chunk的heap_slot不再使用
	    chunk->heap = heap;
	    chunk->next = chunk;
	    chunk->prev = chunk;
	    chunk->free_pages = ZEND_MM_PAGES - ZEND_MM_FIRST_PAGE; //剩余可用page数
	    chunk->free_tail = ZEND_MM_FIRST_PAGE;
	    chunk->num = 0;
	    chunk->free_map[0] = (Z_L(1) << ZEND_MM_FIRST_PAGE) - 1; //将第一个page的bit分配标识位设置为1
	    chunk->map[0] = ZEND_MM_LRUN(ZEND_MM_FIRST_PAGE); //第一个page的类型为ZEND_MM_IS_LRUN，即large内存
	    heap->main_chunk = chunk; //指向主chunk
	    heap->cached_chunks = NULL; //缓存chunk链表
	    heap->chunks_count = 1; //已分配chunk数
	    heap->peak_chunks_count = 1;
	    heap->cached_chunks_count = 0;
	    heap->avg_chunks_count = 1.0;
	    ...
	    heap->huge_list = NULL; //huge内存链表
	    return heap;
	}


超过2M内存的申请，与通用的内存申请没有太大差别，只是将申请的内存块通过单链表进行了管理。
chunk是ZendMM向系统申请内存的唯一粒度。在申请chunk内存时有一个关键操作，那就是将内存地址对齐到ZEND_MM_CHUNK_SIZE，
也就是说申请的chunk地址都是ZEND_MM_CHUNK_SIZE的整数倍

small内存总共有30种固定大小的规格：8,16,24,32,40,48,56,64,80,96,112,128 ... 1792,2048,2560,3072 Byte，我们把这称之为slot，
这些slot的大小是有规律的:最小的slot大小为8byte，前8个slot__依次递增8byte__，后面每隔4个递增值乘以2，即slot 0-7递增8byte、8-11递增16byte、12-15递增32byte、16-19递增32byte、20-23递增128byte、24-27递增256byte、28-29递增512byte，每种大小的slot占用的page数分别是：slot 0-15各占1个page、slot 16-29依次占5, 3, 1, 1, 5, 3, 2, 2, 5, 3, 7, 4, 5, 3个page，这些值定义在zend_alloc_sizes.h中

每次request请求结束会对内存池进行一次清理，检查cache的chunk数是否超过均值，超过的话就进行清理
内存池会维持一定的chunk数，每次释放并不会直接销毁而是加入到cached_chunks中，这样下次申请chunk时直接就用了，同时为了防止占用过多内存，
cached_chunks会根据每次request请求计算的chunk使用均值保证其维持在一定范围内。

slot回收 头插法

#### 参考资料

https://github.com/pangudashu/php7-internal/blob/master/5/zend_alloc.md
