
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

sqlite 性能的提升得益于 `PRAGMA SYNCHRONOUS=OFF` 指令. 但此仅极大提高插入,修改,删除,速度;
查询速度始终都是最快的.

与 `'PRAGMA CACHE_SIZE=50000', 'PRAGMA TEMP_STORE=MEMORY'` 和是否在`/dev/shm/`中无关

`PRAGMA JOURNAL_MODE=OFF` 也可提升写性能

```
test_1 : insert 0.117552 , select 0.232488 , update 0.152223, delete 0.159850
test_2 : insert 4.211176 , select 0.236098 , update 4.206038, delete 4.425278
test_3 : insert 0.076462 , select 0.218319 , update 0.117334, delete 0.101830
sqlite : insert 0.051241 , select 0.023352 , update 0.044922, delete 0.044684
```

`PRAGMA LOCKING_MODE=EXCLUSIVE` 模式更能大幅提高`sqlite`整体性能

https://phiresky.github.io/blog/2020/sqlite-performance-tuning/


```
test_1 : insert 0.120191 , select 0.232615 , update 0.156415, delete 0.168097
test_2 : insert 4.186871 , select 0.256714 , update 4.610582, delete 4.275338
test_3 : insert 0.076355 , select 0.240730 , update 0.121399, delete 0.105168
sqlite : insert 0.022969 , select 0.008269 , update 0.017206, delete 0.016815
```


考虑到`memory`引擎存在表大小限制,且是非持久化的,用`myiasm`更划算



测试时需要用127.0.0.1去测试, 使用局域网测试速度偏差较大.要慢好几倍


