## 空间问题

最近看一下php7源码

其中有个`string`的`struct`

    struct _zend_string {
            zend_refcounted_h gc;
            zend_ulong        h;                /* hash value */
            size_t            len;
            char              val[1];
    };
    
一直想不通为什么char数组而且是一个呢，为什么不是`char*`
终于想明白啦，因为最后用的val,它只是一个指向而已，而且`char[1]`的占位1个字节，
如果用`char*` 就是一个指针字节（32系统就是4字节，64位系统就是8字节）空间问题呀

