<?php


$str1 = file_get_contents('./1');
$str2 = file_get_contents('./2');
$str3 = file_get_contents('./3');

$len1 = strlen($str1);
$len2 = strlen($str2);
$len3 = strlen($str3);

$arr = [1, 0.75, 0.5, 0.25, 0.2, 0.18, 0.17, 0.16,  0.15, 0.135, 0.125];

foreach ($arr as $n) {
    $l = intval($len1 * $n);
    $s = substr($str1, 0, $l);
    $o = gzencode($s);
    $ok = strlen($o) >= $l ? ' bad' : '';
    echo "str1: $l => ", strlen($o), $ok, PHP_EOL;
}


foreach ($arr as $n) {
    $l = intval($len2 * $n);
    $s = substr($str2, 0, $l);
    $o = gzencode($s);
    $ok = strlen($o) >= $l ? ' bad' : '';
    echo "str2: $l => ", strlen($o), $ok, PHP_EOL;
}


foreach ($arr as $n) {
    $l = intval($len3 * $n);
    $s = substr($str3, 0, $l);
    $o = gzencode($s);
    $ok = strlen($o) >= $l ? ' bad' : '';
    echo "str3: $l => ", strlen($o), $ok, PHP_EOL;
}

// 测试表明, 大概在160字节以内,gzip压缩无法再缩减体积.
