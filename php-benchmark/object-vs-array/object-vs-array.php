<?php

class SomeClass 
{
    public $aaa;
    public $bbb;
    public $ccc;
}

function test1()
{
    $t0 = microtime(true);
    $arraysOf=[];
    $inicio=memory_get_usage(); 
    for ($i=0; $i<1000; $i++) 
    {
        $z = [];
        for ($j=0; $j<1000; $j++) 
        {
            $z['aaa'] = 'aaa';
            $z['bbb'] = 'bbb';
            $z['ccc'] = $z['aaa'].$z['bbb'];            
        }
        $arraysOf[]=$z;
    }
    $fin=memory_get_usage();
    echo 'array: '.(microtime(true)-$t0).PHP_EOL;
    echo 'memory: '.($fin-$inicio).PHP_EOL;
    print_r($z);
    echo PHP_EOL;
}

function test2()
{

    $t0 = microtime(true);
    $arraysOf=[];
    $inicio=memory_get_usage(); 
    for ($i=0; $i<1000; $i++) 
    {
        $z = new SomeClass();
        for ($j=0; $j<1000; $j++) 
        {
            $z->aaa = 'aaa';
            $z->bbb = 'bbb';
            $z->ccc = $z->aaa.$z->bbb;          
        }
        $arraysOf[]=$z;
    }
    $fin=memory_get_usage();    
    echo 'SomeClass: '.(microtime(true)-$t0).PHP_EOL;
    echo 'memory: '.($fin-$inicio).PHP_EOL;
    print_r($z);
    echo PHP_EOL;
}

function test3()
{

    $t0 = microtime(true);
    $arraysOf=[];
    $inicio=memory_get_usage(); 
    for ($i=0; $i<1000; $i++) 
    {
        $z = new stdClass();
        for ($j=0; $j<1000; $j++) {
            $z->aaa = 'aaa';
            $z->bbb = 'bbb';
            $z->ccc = $z->aaa.$z->bbb;          
        }
        $arraysOf[]=$z;
    }
    $fin=memory_get_usage();    
    $fin=memory_get_usage();    
    echo 'stdClass: '.(microtime(true)-$t0).PHP_EOL;
    echo 'memory: '.($fin-$inicio).PHP_EOL;
    print_r($z);
    echo PHP_EOL;
}
echo 'php:'.phpversion().PHP_EOL.PHP_EOL;
test1();
test2();
test3();




