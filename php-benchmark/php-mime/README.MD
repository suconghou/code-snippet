1. 对于 图片可监测的方法较多

gd 库中的

`getimagesize` 不仅可以取得类型，还能取得图片大小等信息

`list($width, $height, $type, $attr) = getimagesize("img/flag.jpg");`

`image_type_to_mime_type` 常量转mime

`image_type_to_extension` 常量转后缀名

2. exif 扩展 

`exif_imagetype` 只读取文件头部几字节，相比 `getimagesize` 速度更快

返回常量 及其值 可见 http://php.net/manual/en/function.image-type-to-mime-type.php


3. fileinfo 扩展

mime_content_type 直接检测文件信息， 给出 `mime`

```
getimagesize took 1.1480870246887 seconds.
exif_imagetype took 0.92027592658997 seconds.
mime_content_type took 2.9834370613098 seconds.
minimime took 1.0923340320587 seconds.
```

`mime_content_type` 是最慢的

`exif_imagetype`是最快的

`getimagesize` 和  `exif_imagetype` 如果不是图片,受文件大小而影响速度

`minimime` 只检测前几个字节,始终都是比较快的