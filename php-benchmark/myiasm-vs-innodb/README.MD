
单线程 `myisam` `innodb` `memory` 性能对比


insert, update , delete 各测试500次

```
test_1 : insert 0.121150 , select 0.256541 , update 0.152954, delete 0.165190
test_2 : insert 4.231037 , select 0.252337 , update 4.422895, delete 4.508884
test_3 : insert 0.107814 , select 0.307510 , update 0.142620, delete 0.125195
sqlite : insert 0.198171 , select 0.024040 , update 0.179844, delete 0.188927


test_1 : insert 0.119780 , select 0.267417 , update 0.159814, delete 0.165611
test_2 : insert 4.190990 , select 0.241082 , update 4.217887, delete 4.341928
test_3 : insert 0.075814 , select 0.226937 , update 0.114106, delete 0.102527
sqlite : insert 0.125249 , select 0.025842 , update 0.110842, delete 0.121395
```

> 单线程 `myisam` 无论是insert,还是update,delete 都比 `innodb` 快

> `myisam` select 与 `innodb` 基本持平

> `innodb`写操作较慢,应该是其行锁导致,多线程并发下,才有优势

> `memory`引擎综合来看略微比`myiasm`快点,但优势不大

> `memory` 和 `myisam` 插入比查询快


插入较快的 0.246ms 完成一次操作 每秒处理 4067 次

查询较快的 0.499ms 完成一次操作 每秒处理 2004 次

> `sqlite` 的查询比其他的快10倍,单次约 0.048ms  每秒处理 20798 次, 查询轻度使用相当于redis





考虑到`memory`引擎存在表大小限制,且是非持久化的,用`myiasm`更划算



测试时需要用127.0.0.1去测试, 使用局域网测试速度偏差较大.要慢好几倍


