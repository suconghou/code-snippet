
## php object vs array

基于 https://stackoverflow.com/questions/2193049/php-objects-vs-arrays

php5.6 测试



1. object use less memory (54% of arrays)
2. array is faster than object
3. stdClass is evil


php7.1 测试


1. object use less memory (37% of arrays)
2. object is faster than array
3. stdClass is evil

```
php:7.3.11

array: 0.09838080406189
memory: 444920
Array
(
    [aaa] => aaa
    [bbb] => bbb
    [ccc] => aaabbb
)

SomeClass: 0.07353401184082
memory: 164920
SomeClass Object
(
    [aaa] => aaa
    [bbb] => bbb
    [ccc] => aaabbb
)

stdClass: 0.092514991760254
memory: 484920
stdClass Object
(
    [aaa] => aaa
    [bbb] => bbb
    [ccc] => aaabbb
)


```


```
php:8.0.1

array: 0.096072912216187
memory: 444920
Array
(
    [aaa] => aaa
    [bbb] => bbb
    [ccc] => aaabbb
)

SomeClass: 0.067854166030884
memory: 164920
SomeClass Object
(
    [aaa] => aaa
    [bbb] => bbb
    [ccc] => aaabbb
)

stdClass: 0.084095001220703
memory: 484920
stdClass Object
(
    [aaa] => aaa
    [bbb] => bbb
    [ccc] => aaabbb
)
```

1. 数组始终没有对象快
2. stdClass 占用内存最大
3. 同样数据,对比数组,对象仅使用37%的内存