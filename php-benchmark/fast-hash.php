<?php

set_time_limit(720);

$begin = microtime(true);
$scores = [];


foreach (hash_algos() as $algo) {
    $scores[$algo] = 0;
}

for ($i = 0; $i < 5e4; $i++) {
    $number = random_int(10000000000000, 100000000000000);
    $string = randomString(30);

    foreach (hash_algos() as $algo) {
        $start = microtime(true);

        hash($algo, $number); //Number
        hash($algo, $string); //String

        $end = endTime($start);

        $scores[$algo] += $end;
    }
}


asort($scores);

$i = 1;
foreach ($scores as $alg => $time) {
    print $i . ' - ' . $alg . ' ' . $time . PHP_EOL;
    $i++;
}

echo "Entire page took " . endTime($begin) . ' seconds', PHP_EOL;

echo "Hashes Compared", PHP_EOL;

foreach ($scores as $alg => $time) {
    print $i . ' - ' . $alg . ' ' . hash($alg, $string) . PHP_EOL;
    $i++;
}


function endTime($starttime)
{
    $endtime = microtime(true);
    return ($endtime - $starttime);
}

function randomString($length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $string = '';
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $string;
}
