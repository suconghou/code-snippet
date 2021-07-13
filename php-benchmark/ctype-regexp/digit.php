<?php

error_reporting(E_ALL);

$time = microtime(true);
for ($i = 90000; $i--;) {
    preg_match('/^\d+$/', "$i");
}
echo ("preg_match took " . (microtime(true) - $time) . " seconds." . PHP_EOL);


$time = microtime(true);
for ($i = 90000; $i--;) {
    ctype_digit("$i");
}
echo ("ctype_digit took " . (microtime(true) - $time) . " seconds." . PHP_EOL);


$time = microtime(true);
for ($i = 90000; $i--;) {
    filter_var($i, FILTER_VALIDATE_INT);
}
echo ("filter_var took " . (microtime(true) - $time) . " seconds." . PHP_EOL);




// 以下6项均是false
var_dump(is_int('5'));
var_dump(is_float(5));
var_dump(is_float('5'));
var_dump(is_float('5.'));
var_dump(is_float('.5'));
var_dump(is_float('5.0'));

echo PHP_EOL, 'is_numeric', PHP_EOL, PHP_EOL;
// 以下6项均为true
var_dump(is_numeric(5));
var_dump(is_numeric(5.0));
var_dump(is_numeric('5'));
var_dump(is_numeric('5.'));
var_dump(is_numeric('.5'));
var_dump(is_numeric('5.0'));

echo PHP_EOL, 'ctype_digit', PHP_EOL, PHP_EOL;
// ctype_digit 输入整数会被内部转为ascii char 再判断
var_dump(ctype_digit(5));
var_dump(ctype_digit(5.0));
var_dump(ctype_digit('5')); // 只有这个为true
var_dump(ctype_digit('5.'));
var_dump(ctype_digit('.5'));
var_dump(ctype_digit('5.0'));

echo PHP_EOL, 'filter_var', PHP_EOL, PHP_EOL;
// 后三个不合法
var_dump(filter_var(5, FILTER_VALIDATE_INT));
var_dump(filter_var(5.0, FILTER_VALIDATE_INT));
var_dump(filter_var('5', FILTER_VALIDATE_INT));
var_dump(filter_var('5.0', FILTER_VALIDATE_INT));
var_dump(filter_var('.5', FILTER_VALIDATE_INT));
var_dump(filter_var('5.', FILTER_VALIDATE_INT));
var_dump(filter_var('05', FILTER_VALIDATE_INT));
