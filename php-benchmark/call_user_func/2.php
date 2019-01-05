<?php error_reporting(E_ALL);
function test() {}
$nIter = 1000000;
$argNums = [0, 1, 2, 3, 4, 5, 100];
$func = 'test';
foreach ($argNums as $argNum) {
    $args = $argNum == 0 ? [] : array_fill(0, $argNum, null);
    $startTime = microtime(true);
    for ($i = 0; $i < $nIter; ++$i) {
        call_user_func_array($func, $args);
    }
    $endTime = microtime(true);
    echo "cufa   with $argNum args took ", $endTime - $startTime, "\n";
    $startTime = microtime(true);
    for ($i = 0; $i < $nIter; ++$i) {
        switch (count($args)) {
            case 0: $func(); break;
            case 1: $func($args[0]); break;
            case 2: $func($args[0], $args[1]); break;
            case 3: $func($args[0], $args[1], $args[2]); break;
            case 4: $func($args[0], $args[1], $args[2], $args[3]); break;
            case 5: $func($args[0], $args[1], $args[2], $args[3], $args[4]); break;
            default: call_user_func_array($func, $args); break;
        }
    }
    $endTime = microtime(true);
    echo "switch with $argNum args took ", $endTime - $startTime, "\n";
    $startTime = microtime(true);
    for ($i = 0; $i < $nIter; ++$i) {
        $func(...$args);
    }
    $endTime = microtime(true);
    echo "unpack with $argNum args took ", $endTime - $startTime, "\n";
}