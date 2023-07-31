
## jit

opcode.jit


它接受disable，on，off，trace，function，和按顺序排列的 4 个不同标志的 4 位值。

disable：在启动时完全禁用JIT功能，并且在运行时无法启用。
off：禁用，但是可以在运行时启用JIT。
on：启用tracing模式。
tracing：细化配置 的别名1254。
function：细化配置 的别名1205。

这opcache.jit是可选的。如果忽略该属性，则JIT将使用默认值。

您问哪个默认值？那会是opcache.jit=tracing

4位配置值的格式为CRTO，其中每个位置均允许字母表示的标志的单个数字值。

＃C — CPU特定的优化标志
0	没有
1	启用AVX指令生成

＃R-寄存器分配
0	不使用寄存器分配
1	使用本地线性扫描寄存器分配器
2	使用全局线性扫描寄存器分配器

＃T — JIT触发器
0	JIT第一次脚本加载时的所有函数
1	首次执行时JIT函数
2	在一次运行后，JIT调用次数最多的百分之(opcache.prof_threshold * 100)的函数
3	当函数/方法执行超过N(N和opcache.jit_hot_func相关)次以后JIT
4	当函数方法的注释中含有@jit的时候对它进行JIT
5	当一个Trace执行超过N次（和opcache.jit_hot_loop, jit_hot_return等有关)以后JIT

＃O-优化级别
0	不要JIT
1	最小JIT（调用标准VM处理程序）
2	选择性VM处理程序内联
3	基于单个函数的静态类型推断的优化JIT
4	基于静态类型推断和调用树的优化JIT
5	基于静态类型推断和内部过程分析的优化JIT




1255将最大程度地jitting，使用跟踪JIT，使用全局线性扫描寄存器分配器（无论如何）并启用AVX指令生成。




swoole-cli -d opcache.enable_cli=0 testjit.php

```
24157817
1.3199150562286
string(19) "opcache is disabled"
```

swoole-cli -d opcache.enable_cli=1 -d opcache.jit=tracing -d opcache.jit_buffer_size=64M testjit.php

```
24157817
0.25373601913452
array(7) {
  ["enabled"]=>
  bool(true)
  ["on"]=>
  bool(true)
  ["kind"]=>
  int(5)
  ["opt_level"]=>
  int(4)
  ["opt_flags"]=>
  int(6)
  ["buffer_size"]=>
  int(67108848)
  ["buffer_free"]=>
  int(67103520)
}

```

swoole-cli -d opcache.enable_cli=1 -d opcache.jit=function -d opcache.jit_buffer_size=64M testjit.php

```
24157817
0.40308713912964
array(7) {
  ["enabled"]=>
  bool(true)
  ["on"]=>
  bool(true)
  ["kind"]=>
  int(0)
  ["opt_level"]=>
  int(5)
  ["opt_flags"]=>
  int(6)
  ["buffer_size"]=>
  int(67108848)
  ["buffer_free"]=>
  int(67101824)
}
```

swoole-cli -d opcache.enable_cli=1 -d opcache.jit=1255 -d opcache.jit_buffer_size=64M testjit.php

```
24157817
0.24912095069885
array(7) {
  ["enabled"]=>
  bool(true)
  ["on"]=>
  bool(true)
  ["kind"]=>
  int(5)
  ["opt_level"]=>
  int(5)
  ["opt_flags"]=>
  int(6)
  ["buffer_size"]=>
  int(67108848)
  ["buffer_free"]=>
  int(67103520)
}
```

开启jit提升了5倍性能

这个案例
opcache.jit=tracing 的效果优于 opcache.jit=function

opcache.jit=tracing 与 opcache.jit=1255 效果类似

opcache.jit_buffer_size 必须配置 否则jit是disabled ，建议至少1M

opcache.jit_buffer_size 分配的更大，对测试没有影响
