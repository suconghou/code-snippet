<?php


function code(int $nc, $a = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'): string
{
    $l = strlen($a) - 1;
    $r = '';
    while ($nc-- > 0) $r .= $a[mt_rand(0, $l)];
    return $r;
}

function random_string(int $nc): string
{
    $bytes = random_bytes($nc / 2);
    return bin2hex($bytes);
}

function random_string2(int $nc): string
{
    $bytes = openssl_random_pseudo_bytes($nc / 2);
    return bin2hex($bytes);
}


function test1()
{
    $t0 = microtime(true);

    for ($i = 0; $i < 500000; $i++) {
        code(20);
    }
    echo 'code: ' . (microtime(true) - $t0) . PHP_EOL;
}



function test2()
{
    $t0 = microtime(true);
    for ($i = 0; $i < 500000; $i++) {
        random_string(20);
    }
    echo 'random_string: ' . (microtime(true) - $t0) . PHP_EOL;
}



function test2_2()
{
    $t0 = microtime(true);
    for ($i = 0; $i < 500000; $i++) {
        random_string2(20);
    }
    echo 'openssl_random_pseudo_bytes: ' . (microtime(true) - $t0) . PHP_EOL;
}




function test3()
{
    $t0 = microtime(true);

    for ($i = 0; $i < 500000; $i++) {
        random_int(1, 9999);
    }
    echo 'random_int: ' . (microtime(true) - $t0) . PHP_EOL;
}


function test4()
{
    $t0 = microtime(true);

    for ($i = 0; $i < 500000; $i++) {
        rand(1, 9999);
    }
    echo 'rand: ' . (microtime(true) - $t0) . PHP_EOL;
}


function test5()
{
    $t0 = microtime(true);

    for ($i = 0; $i < 500000; $i++) {
        mt_rand(1, 9999);
    }
    echo 'mt_rand: ' . (microtime(true) - $t0) . PHP_EOL;
}








echo PHP_VERSION, "\n";

test1();
test2();
test2_2();
test3();
test4();
test5();
