闪库跑路.

![](.static_images/f68660bd.png)

正确写法

![](.static_images/e9818d79.png)

正确写法

![](.static_images/21a7e7ac.png)

这个的原因主要是因为 laravel不支持 将 字符串设置为主键.
如果非得 使用字符串作为主键时 ，必须将 明确指明另外两个属性

    protected $table = 'rooms';
    protected $primaryKey = 'room_id'; // 如果是主键字符串，被强制设置主键，并且没有设置$incrementing和$keyType，主键值会被强制转换
    public $incrementing = false; // 非递增或者非数字的主键
    protected $keyType = 'string'; // 主键不是一个整数，则应该在模型上设置

. 

按照原有逻辑. 

    select * from rooms where room_id=0
    
如果room_id 是一个字符串.那么 sql查询时 会隐式转换. 将所有的记录查询出来。全部删除.