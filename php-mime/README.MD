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

