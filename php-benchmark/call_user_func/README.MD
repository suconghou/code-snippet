
`php -v && php 1.php && php 2.php`

```
PHP 7.1.19 (cli) (built: Aug 17 2018 20:10:18) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.1.0, Copyright (c) 1998-2018 Zend Technologies

Variable functions took 0.0084218978881836 seconds.
call_user_func took 0.0071558952331543 seconds.
eval took 0.23561906814575 seconds.
cufa   with 0 args took 0.058526992797852
switch with 0 args took 0.084620952606201
unpack with 0 args took 0.053608894348145
cufa   with 1 args took 0.061805009841919
switch with 1 args took 0.10113191604614
unpack with 1 args took 0.061662197113037
cufa   with 2 args took 0.066169023513794
switch with 2 args took 0.11916899681091
unpack with 2 args took 0.065468072891235
cufa   with 3 args took 0.067879915237427
switch with 3 args took 0.13740491867065
unpack with 3 args took 0.070169925689697
cufa   with 4 args took 0.072452068328857
switch with 4 args took 0.18104791641235
unpack with 4 args took 0.074442148208618
cufa   with 5 args took 0.074589967727661
switch with 5 args took 0.18765783309937
unpack with 5 args took 0.079974889755249
cufa   with 100 args took 0.36465096473694
switch with 100 args took 0.44300007820129
unpack with 100 args took 0.52764296531677
```


```
PHP 7.2.13 (cli) (built: Dec 29 2018 07:45:32) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.2.0, Copyright (c) 1998-2018 Zend Technologies

Variable functions took 0.014910936355591 seconds.
call_user_func took 0.012983798980713 seconds.
eval took 0.36304712295532 seconds.
cufa   with 0 args took 0.10280084609985
switch with 0 args took 0.15068888664246
unpack with 0 args took 0.097218036651611
cufa   with 1 args took 0.11106896400452
switch with 1 args took 0.18191504478455
unpack with 1 args took 0.10195589065552
cufa   with 2 args took 0.11092710494995
switch with 2 args took 0.21194195747375
unpack with 2 args took 0.10492491722107
cufa   with 3 args took 0.1202540397644
switch with 3 args took 0.24090194702148
unpack with 3 args took 0.11088585853577
cufa   with 4 args took 0.12360382080078
switch with 4 args took 0.25838398933411
unpack with 4 args took 0.11192488670349
cufa   with 5 args took 0.12179613113403
switch with 5 args took 0.27834677696228
unpack with 5 args took 0.11759495735168
cufa   with 100 args took 0.4373950958252
switch with 100 args took 0.48877477645874
unpack with 100 args took 0.46461892127991
```

之前有说法说`call_user_func` `call_user_func_array` 比起其他调用会慢.

现在测试PHP7中,已经不算慢了,和其他调用相比无太多差别,参数多时还会更快.

顺便说从PHP5.6起的参数解构语法也很快 `$func(...$values);`