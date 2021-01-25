## 为何redis中的slot数量为16384

redis cluster slots数量 为何是16384（2的14次方）
redis cluster集群通过分片的方式来保存数据库中键值对：集群的整个数据库被分为16384个槽（slot）,
数据库中的每个键都属于这16384个槽的其中一个，集群中的每个节点可以处理0个或者最多16384个槽
当数据库中的16384个槽都有节点在处理时，集群处于上线状态（ok）；相反地，如果数据库中有
任何一个槽没有得到处理，那么集群处于下线状态（fail）.

The reason is:

Normal heartbeat packets carry the full configuration of a node, that can be replaced in an idempotent way with the old in order to update an old config. This means they contain the slots configuration for a node, in raw form, that uses 2k of space with16k slots, but would use a prohibitive 8k of space using 65k slots.
At the same time it is unlikely that Redis Cluster would scale to more than 1000 mater nodes because of other design tradeoffs.
So 16k was in the right range to ensure enough slots per master with a max of 1000 maters, but a small enough number to propagate the slot configuration as a raw bitmap easily. Note that in small clusters the bitmap would be hard to compress because when N is small the bitmap would have slots/N bits set that is a large percentage of bits set.

翻译

1、正常的心跳包携带节点的完整配置，可以用幂等方式替换旧节点以更新旧配置。 这意味着它们包含原始形式的节点的插槽配置，它使用带有16k插槽的2k空间，但使用65k插槽时将使用高达8k的空间。
2、同时，由于其他设计权衡，Redis Cluster不太可能扩展到超过1000个主节点。
因此，16k处于正确的范围内，以确保每个主站有足够的插槽，最多1000个主站，但足够小的数字可以轻松地将插槽配置传播为原始位图。 请注意，在小型集群中，位图难以压缩，因为当N很小时，位图将设置插槽/ N位，这是设置的大部分位。