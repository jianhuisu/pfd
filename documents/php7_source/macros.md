## php源码中的常见宏

CG # define CG(v) (compiler_globals.v)  compile 编译宏
EG # define EG(v) (executor_globals.v)  executor 执行宏 EG资源的结构是zend_executor_globals，所以这个值就是sizeof(zend_executor_globals) 
PG # define PG(v) (core_globals.v)      
SG # define SG(v) (sapi_globals.v) 
AG # define AG(v) alloc_globals 是一个全局变量 

    int main(void)
    {
        if (PG(internal_encoding) && PG(internal_encoding)[0]) {
            return PG(internal_encoding);
        } else if (SG(default_charset)) {
            return SG(default_charset);
        }
    }

after expand :
    
    int main(void)
    {
        if ((core_globals.internal_encoding) && (core_globals.internal_encoding)[0]) {
            return (core_globals.internal_encoding);
        } else if ((sapi_globals.default_charset)) {
            return (sapi_globals.default_charset);
        }
    }
    
    
编译变量 IS_CV  compile_variable

PHP7的性能, 我们并没有引入什么新的技术模式, 不过就是主要来自, 持续不懈的降低内存占用,
提高缓存友好性, 降低执行的指令数的这些原则而来的, 可以说PHP7的重构就是这三个原则.
