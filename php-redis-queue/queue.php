<?php 


$host = '192.168.10.9';
$port = 6379;
$dbIndex=1;

$redis = new Redis();

$redis->connect($host, $port);

$redis->select($dbIndex);

// $redis->getOption(Redis::OPT_SERIALIZER);	// return Redis::SERIALIZER_NONE, Redis::SERIALIZER_PHP, or Redis::SERIALIZER_IGBINARY.

$redis->setOption(Redis::OPT_SERIALIZER,Redis::SERIALIZER_IGBINARY);

$redis->set("hello",[1,3,5]);

var_dump($redis->get("hello"));

$task = $redis->brPop('mailQueue', 10);



