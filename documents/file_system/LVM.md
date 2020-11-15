# LVM

`LVM（Logical Volume Manager）` 逻辑卷管理。

它是对磁盘分区进行管理的一种机制，建立在硬盘和分区之上的一个逻辑层，用来提高磁盘管理的灵活性。
通过LVM可将若干个磁盘分区连接为一个整块的卷组(`Volume Group`)，形成一个存储池。
可以在卷组上随意创建逻辑卷(`Logical Volumes`)，并进一步在逻辑卷上创建文件系统，与直接使用物理存储在管理上相比，提供了更好灵活性。
(just like distributed file system 像不像分布式文件系统的概念).

`Device Mapper`是`Linux2.6`内核中支持逻辑卷管理的通用设备映射机制，它为实现用于存储资源管理的块设备驱动提供了一个高度模块化的内核架构。

Device Mapper需要两个依赖

 - device-mapper-persistent-data
 - lvm2

在安装docker之前,我们需要




