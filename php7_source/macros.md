## php源码中的常见宏

CG # define CG(v) (compiler_globals.v)  compile 编译宏
EG # define EG(v) (executor_globals.v)  executor 执行宏  
PG # define PG(v) (core_globals.v)      
SG # define SG(v) (sapi_globals.v) 

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