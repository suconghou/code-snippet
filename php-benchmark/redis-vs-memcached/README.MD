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