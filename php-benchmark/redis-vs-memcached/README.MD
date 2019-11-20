
### redis vs memcached

分别测试5万次set,get

redis 3.2.5 vs redis 5.0.4 性能无明显差异

单线程测试 redis 与 memcached性能 基本持平

```
string(9) "redis set"
float(4.7662501335144)
string(9) "redis get"
float(4.3899481296539)
string(5) "49999"
string(7) "mem set"
float(5.0357949733734)
string(7) "mem get"
float(4.6678378582001)
int(49999)
```

平均单次操作消耗0.1ms, 每秒处理1.1万次


### redis vs swoole vs shmop vs shm filesystem


测试 set , get 两万次

```
redis : set 2.237406 , get 1.729676 , delete 1.841666
swoole : set 0.025635 , get 0.023269 , delete 0.017061
shm file : set 0.769856 , get 0.644929 , delete 0.294133
shm_put : set 4.322265 , get 4.323147 , delete 5.154157
shmop : set 0.007536 
```

两千次
```
redis : set 0.188906 , get 0.181506 , delete 0.185384
swoole : set 0.002655 , get 0.002313 , delete 0.001670
shm file : set 0.081780 , get 0.064378 , delete 0.027966
shm_put : set 0.039145 , get 0.038052 , delete 0.041297
shmop : set 0.000772 
```


> 使用tcp通信的redis 在这次测试中是最慢的

> 使用shm内存文件系统比redis稍快, shm 和 redis 比swoole慢的多

> swoole 单次操作基本0.0012ms, 每秒可处理96.4万次

> shm_put_var,shm_get_var 等操作在键多的情况下,效率很差
> 1000个键以内效率很高,当大于9000个键时性能已不及redis
> 100个键以内,性能才能与swoole相当

> shmop 如果不算open与close时间, 速度是比swoole快的.
> 算上open与close时间,性能是不如swoole,相差几十倍


```
swoole : set 0.005579 , get 0.004952 , delete 0.003488
```

CPU性能提升,对swoole table也能大幅提升性能

较好的CPU每秒可执行400万查询


System V信号量，编译时加上 –enable-sysvsem
System V共享内存，编译时加上 –enable-sysvshm
System V消息队列，编译时加上 –enable-sysvmsg
Shared Memory，编译时加上 –enable-shmop

System V IPC系列不支持Windows, 所以如果要在win环境下使用，只能选Shared Memory。