<?php 



ini_set('memory_limit', '512m');
$array = array_fill(0, 1e6, "hello world 你 我 他 ".rand(1e9, 9e9));

testcase($array);

$info = [ 'name' => 'SU','age' => 27,'country' => 'China'];
$array = [];
for($i = 0; $i < 2e5; $i++)
{
    $array[$i] = $info;
}
echo PHP_EOL.PHP_EOL;
testcase($array);


function testcase($array)
{

	$start = microtime(true);
	$export = json_encode($array);
	$end = microtime(true);
	$duration = $end - $start;
	print('JSON Encode: ' . $duration .' Len: '.round(strlen($export)/1024,2).'KB'. PHP_EOL);

	$start = microtime(true);
	$import = json_decode($export);
	$end = microtime(true);
	$duration = $end - $start;
	print('JSON Decode: ' . $duration . PHP_EOL);

	$start = microtime(true);
	$export = serialize($array);
	$end = microtime(true);
	$duration = $end - $start;
	print('Serialize: ' . $duration .' Len: '.round(strlen($export)/1024,2).'KB'. PHP_EOL);

	$start = microtime(true);
	$import = unserialize($export);
	$end = microtime(true);
	$duration = $end - $start;
	print('Unserialize: ' . $duration . PHP_EOL);

	$start = microtime(true);
	$export = igbinary_serialize($array);
	$end = microtime(true);
	$duration = $end - $start;
	print('Igbinary Serialize: ' . $duration .' Len: '.round(strlen($export)/1024,2).'KB'. PHP_EOL);

	$start = microtime(true);
	$import = igbinary_unserialize($export);
	$end = microtime(true);
	$duration = $end - $start;
	print('Igbinary Unserialize: ' . $duration . PHP_EOL);


	$start = microtime(true);
	$export = msgpack_pack($array);
	$end = microtime(true);
	$duration = $end - $start;
	print('Msgpack Serialize: ' . $duration .' Len: '.round(strlen($export)/1024,2).'KB'. PHP_EOL);

	$start = microtime(true);
	$import = msgpack_unpack($export);
	$end = microtime(true);
	$duration = $end - $start;
	print('Msgpack Unserialize: ' . $duration . PHP_EOL);

}
