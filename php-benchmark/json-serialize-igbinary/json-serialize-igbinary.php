<?php 



ini_set('memory_limit', '512m');
$array = array_fill(0, 1000000, "hello world 你 我 他 ".rand(1, 1e9));

$start = microtime(true);
$export = json_encode($array);
$end = microtime(true);
$duration = $end - $start;
print('JSON Encode: ' . $duration . PHP_EOL);

$start = microtime(true);
$import = json_decode($export);
$end = microtime(true);
$duration = $end - $start;
print('JSON Decode: ' . $duration . PHP_EOL);

$start = microtime(true);
$export = serialize($array);
$end = microtime(true);
$duration = $end - $start;
print('Serialize: ' . $duration . PHP_EOL);

$start = microtime(true);
$import = unserialize($export);
$end = microtime(true);
$duration = $end - $start;
print('Unserialize: ' . $duration . PHP_EOL);

$start = microtime(true);
$export = igbinary_serialize($array);
$end = microtime(true);
$duration = $end - $start;
print('Igbinary Serialize: ' . $duration . PHP_EOL);

$start = microtime(true);
$import = igbinary_unserialize($export);
$end = microtime(true);
$duration = $end - $start;
print('Igbinary Unserialize: ' . $duration . PHP_EOL);


