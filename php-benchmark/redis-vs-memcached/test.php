<?php 

$redis = new Redis();
$redis->connect('127.0.0.1',6379);
$redis->auth('123456');

var_dump('redis version:',$redis->info());

$mem = new Memcached();
$mem->addServer('127.0.0.1',11211);

var_dump('memcached version:',$mem->getVersion());


$t1 = microtime(true);
for($i=0;$i<5e4;$i++)
{
    $redis->set('key'.$i,$i,99);
}
var_dump('redis set',microtime(true)-$t1);


$t1 = microtime(true);
for($i=0;$i<5e4;$i++)
{
    $last=$redis->get('key'.$i);
}
var_dump('redis get',microtime(true)-$t1,$last);



$t1 = microtime(true);
for($i=0;$i<5e4;$i++)
{
    $mem->set('key'.$i,$i);
}
var_dump('mem set',microtime(true)-$t1);


$t1 = microtime(true);
for($i=0;$i<5e4;$i++)
{
    $last=$mem->get('key'.$i);
}
var_dump('mem get',microtime(true)-$t1,$last);


