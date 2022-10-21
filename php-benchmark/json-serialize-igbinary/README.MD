## json vs serialize vs igbinary



`php -v && php json-serialize-igbinary.php`

```
PHP 7.2.12 (cli) (built: Nov 14 2018 06:12:01) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.2.0, Copyright (c) 1998-2018 Zend Technologies
JSON Encode: 0.18070411682129 Len: 44921.88KB
JSON Decode: 0.40581202507019
Serialize: 0.2348940372467 Len: 49696.19KB
Unserialize: 0.13200116157532
Igbinary Serialize: 0.050822973251343 Len: 6707.73KB
Igbinary Unserialize: 0.041654109954834
Msgpack Serialize: 0.12111306190491 Len: 35156.25KB
Msgpack Unserialize: 0.060525178909302


JSON Encode: 0.093935012817383 Len: 17773.44KB
JSON Decode: 0.32733702659607
Serialize: 0.10123610496521 Len: 20008.69KB
Unserialize: 0.21535086631775
Igbinary Serialize: 0.017636775970459 Len: 1239KB
Igbinary Unserialize: 0.029807090759277
Msgpack Serialize: 0.13679313659668 Len: 10546.88KB
Msgpack Unserialize: 0.40272498130798```
```

```
PHP 7.3.2 (cli) (built: Feb  8 2019 07:30:04) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.3.2, Copyright (c) 1998-2018 Zend Technologies
JSON Encode: 0.34809613227844 Len: 44921.88KB
JSON Decode: 0.58410000801086
Serialize: 0.40364599227905 Len: 49696.19KB
Unserialize: 0.22774505615234
Igbinary Serialize: 0.048108100891113 Len: 6707.73KB
Igbinary Unserialize: 0.046284914016724
Msgpack Serialize: 0.12381887435913 Len: 35156.25KB
Msgpack Unserialize: 0.056283950805664


JSON Encode: 0.12937307357788 Len: 17773.44KB
JSON Decode: 0.33151602745056
Serialize: 0.10629510879517 Len: 20008.69KB
Unserialize: 0.33816504478455
Igbinary Serialize: 0.012787103652954 Len: 1239KB
Igbinary Unserialize: 0.025331020355225
Msgpack Serialize: 0.13252782821655 Len: 10546.88KB
Msgpack Unserialize: 0.39418792724609
```

### Serialize Size

Igbinary is best

### Serialize Speed

Igbinary is best

### Unserialize Speed

Igbinary is best

### other

> Json encode is faster than Serialize

> Json encode is smaller than Serialize

> Json decode is slower than Serialize

`json` `serialize` `export` 三者相比较,大数据量(500kb+)解码数据,serialize最具优势

# gzip 后的比较

寻求一种小数据压缩方案  1-4 KB 数据

对比 msgpack vs json vs json+gzip 

默认压缩级别

```
1json:5619
2json:1037
3json:218

1json+gz:2076
2json+gz:338
3json+gz:169

1msgpack:4384
2msgpack:850
3msgpack:195

1msgpack+gz:2215
2msgpack+gz:367
3msgpack+gz:169
```

数据1 (5619字节) json+gz 最小, msgpack+gz次之

数据2 (1037字节) json+gz 最小,  msgpack+gz次之

数据3 (218字节) msgpack+gz 与 json+gz 一样


`json+gz` 在1000字节以上就能压缩为原来的三分之一

https://www.lucidchart.com/techblog/2019/12/06/json-compression-alternative-binary-formats-and-compression-methods/


https://lobste.rs/s/5edgkf/i_made_jcof_object_notation_which_encodes#c_ndatwo






