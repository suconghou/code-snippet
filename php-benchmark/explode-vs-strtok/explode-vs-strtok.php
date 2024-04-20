<?php

$iterations = 1000000;
$url1 = "/path/to/page/path/to/page/path/to/page/path/to/page/path/to/pagekey1=value1&key2=value2";
$url2 = "/path/to/page/path/to/page/path/to/page/path/to/page/path/to/page?key1=value1&key2=value2";

// 使用 strtok
$start_time = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
  $uri = strtok($url1, '?');
  $uri = strtok($url2, '?');
}
$strtok_time = microtime(true) - $start_time;

// 使用 explode
$start_time = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
  [$uri] = explode('?', $url1, 2);
  [$uri] = explode('?', $url2, 2);
}
$explode_time = microtime(true) - $start_time;

// 输出结果
echo "strtok 用时: $strtok_time 秒\n";
echo "explode 用时: $explode_time 秒\n";

if ($strtok_time < $explode_time) {
  echo "strtok 速度更快\n";
} else {
  echo "explode 速度更快\n";
}