
EBML: Extensible Binary Meta Language



解析EBML 需要了解一个概念

vint 

vint（Unsigned Integer Values of Variable Length）是可变长度无符号整型，比传统32/64位整型更加节省空间。vint有三个部分构成： VINT_WIDTH，VINT_MARKER，VINT_DATA。VINT_MAKRER指的是二进制数据中第一个1的位置；VINT_WIDTH指的是在VINT_MARKER之前的0的个数（可以是0），VINT_WIDTH+1表示对应的vint占用的字节数目

https://github.com/cellar-wg/ebml-specification/blob/master/specification.markdown#variable-size-integer



https://www.webmproject.org/docs/container/



https://gist.github.com/OllieJones/2fef95b433fc442babdc6627e06085a4

