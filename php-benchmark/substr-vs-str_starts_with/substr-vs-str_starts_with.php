<?php

$iterations = 1000000;
$a = file_get_contents('/tmp/1');
$b = file_get_contents('/tmp/2');

// 使用 substr
$start_time = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $x = substr($a, 0, 3) == "\x1f\x8b\x08";
    $x = substr($b, 0, 3) == "\x1f\x8b\x08";
}
$time1 = microtime(true) - $start_time;

// 使用 str_starts_with
$start_time = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $x = str_starts_with($a, "\x1f\x8b\x08");
    $x = str_starts_with($b, "\x1f\x8b\x08");
}
$time2 = microtime(true) - $start_time;

// 使用 strncmp
$start_time = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $x = strncmp($a, "\x1f\x8b\x08", 3) === 0;
    $x = strncmp($b, "\x1f\x8b\x08", 3) === 0;
}
$time3 = microtime(true) - $start_time;

// 使用 substr_compare
$start_time = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $x = substr_compare($a, "\x1f\x8b\x08", 0, 3) === 0;
    $x = substr_compare($b, "\x1f\x8b\x08", 0, 3) === 0;
}
$time4 = microtime(true) - $start_time;


// 输出结果
echo "substr 用时: $time1 秒\n";
echo "str_starts_with 用时: $time2 秒\n";
echo "strncmp 用时: $time3 秒\n";
echo "substr_compare 用时: $time4 秒\n";

$minTime = min($time1, $time2, $time3, $time4);

if ($minTime == $time1) {
    echo "substr 速度最快\n";
} elseif ($minTime == $time2) {
    echo "str_starts_with 速度最快\n";
} elseif ($minTime == $time3) {
    echo "strncmp 速度最快\n";
} else {
    echo "substr_compare 速度最快\n";
}