<?php

$level = 9;

$str1 = file_get_contents('./1');
$str2 = file_get_contents('./2');
$str3 = file_get_contents('./3');


$j1 = json_decode($str1);
$j2 = json_decode($str2);
$j3 = json_decode($str3);

$jj1 = json_encode($j1);
$jj2 = json_encode($j2);
$jj3 = json_encode($j3);


echo "1json:", strlen($jj1), PHP_EOL;
echo "2json:", strlen($jj2), PHP_EOL;
echo "3json:", strlen($jj3), PHP_EOL, PHP_EOL;

$gz1 = gzencode($jj1, $level);
$gz2 = gzencode($jj2, $level);
$gz3 = gzencode($jj3, $level);

echo "1json+gz:", strlen($gz1), PHP_EOL;
echo "2json+gz:", strlen($gz2), PHP_EOL;
echo "3json+gz:", strlen($gz3), PHP_EOL, PHP_EOL;


$m1 = msgpack_pack($j1);
$m2 = msgpack_pack($j2);
$m3 = msgpack_pack($j3);

echo "1msgpack:", strlen($m1), PHP_EOL;
echo "2msgpack:", strlen($m2), PHP_EOL;
echo "3msgpack:", strlen($m3), PHP_EOL, PHP_EOL;


$mg1 = gzencode($m1, $level);
$mg2 = gzencode($m2, $level);
$mg3 = gzencode($m3, $level);


echo "1msgpack+gz:", strlen($mg1), PHP_EOL;
echo "2msgpack+gz:", strlen($mg2), PHP_EOL;
echo "3msgpack+gz:", strlen($mg3), PHP_EOL, PHP_EOL;
