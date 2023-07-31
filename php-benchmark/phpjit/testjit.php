<?php

function feb($n): int
{
    if ($n == 1) {
        return 1;
    }

    if ($n == 2) {
        return 2;
    }

    return feb($n - 1) + feb($n - 2);
}

$n = 36;

$start = microtime(true);

echo feb($n), PHP_EOL;

$end = microtime(true);

echo $end - $start, PHP_EOL;

if (function_exists('opcache_get_status')) {
    $r = opcache_get_status();
    if (!$r) {
        var_dump('opcache is disabled');
    } else {
        var_dump($r['jit']);
    }
} else {
    var_dump('opcache not installed');
}
