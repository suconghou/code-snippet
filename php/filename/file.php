<?php

$name = "中文 文件ab c";

// header("Content-Disposition:" . sprintf("attachment;filename* = UTF-8''%s", rawurlencode($name)));
header("Content-Disposition:" . sprintf("attachment;filename = %s", rawurlencode($name)));
echo $name;
