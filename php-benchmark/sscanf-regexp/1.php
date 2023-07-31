<?php

function pairs(string $data)
{
    $pairs = [];
    foreach (explode(',', $data) as $pair) {
        sscanf($pair, '%[^=]=%s', $key, $value);
        $pairs[$key] = $value;
    }
    return $pairs;
}

function pairs2(string $data)
{
    $pairs = [];
    foreach (explode(',', $data) as $pair) {
        [$key,$value]= explode('=',$pair);
        // sscanf($pair, '%[^=]=%s', $key, $value);
        $pairs[$key] = $value;
    }
    return $pairs;
}


function reg_match(string $data)
{
    $pairs = [];
    if (preg_match_all('/(\w+)=([^,]*)/isu', $data, $matches)) {
        foreach ($matches[0] as $k => $_) {
            $pairs[$matches[1][$k]] = $matches[2][$k];
        }
    }
    return $pairs;
}

$str = "r=e8f095e4fb59bba2W5Z9H1waWftXJjZ1O5kJQUOUs669Sm93,s=bFXbLvWF9YBUDxZ6cBJejQ==,i=10000";

$time = microtime(true);
for ($i = 80000; $i--;) {
    $r = pairs2($str);
}
var_dump($r);
echo ("pairs took " . (microtime(true) - $time) . " seconds." . PHP_EOL);


$time = microtime(true);
for ($i = 80000; $i--;) {
    $r = reg_match($str);
}
var_dump($r);
echo ("reg_match took " . (microtime(true) - $time) . " seconds." . PHP_EOL);
