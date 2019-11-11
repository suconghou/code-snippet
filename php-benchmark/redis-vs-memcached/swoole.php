<?php

// 对比测试 swoole table , redis , shm , shmop


const RUN_TIMES = 2e4;

// 优势多台并发安全
function redis_test()
{
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $redis->auth('123456');
    $arr = [
        ['id' => 0, 'username' => 'hello', 'password' => '6789', 'age' => 20],
        ['id' => 1, 'username' => 'world', 'password' => '1234', 'age' => 22],
    ];
    $str = json_encode($arr);
    $t1 = microtime(true);
    for ($i = 0; $i < RUN_TIMES; $i++) {
        $redis->set('key' . $i, $str, 99);
    }
    $t2 = microtime(true);

    for ($i = 0; $i < RUN_TIMES; $i++) {
        $redis->get('key' . $i);
    }

    $t3 = microtime(true);

    for ($i = 0; $i < RUN_TIMES; $i++) {
        $redis->delete('key' . $i);
    }

    $t4 = microtime(true);

    echo sprintf("redis : set %f , get %f , delete %f", $t2 - $t1, $t3 - $t2, $t4 - $t3), PHP_EOL;
}

// 优势单程序并发安全，随创建进程销毁
function swoole_test()
{
    $arr = [
        ['id' => 0, 'username' => 'hello', 'password' => '6789', 'age' => 20],
        ['id' => 1, 'username' => 'world', 'password' => '1234', 'age' => 22],
    ];
    $str = json_encode($arr);
    $table = new Swoole\Table(6e4);
    $table->column('name', swoole_table::TYPE_STRING, 128);
    $table->create();
    $t1 = microtime(true);
    for ($i = 0; $i < RUN_TIMES; $i++) {
        $table->set("key" . $i, ['name' => $str]);
    }
    $t2 = microtime(true);
    for ($i = 0; $i < RUN_TIMES; $i++) {
        $table->get("key" . $i);
    }
    $t3 = microtime(true);
    for ($i = 0; $i < RUN_TIMES; $i++) {
        $table->del("key" . $i);
    }
    $t4 = microtime(true);
    echo sprintf("swoole : set %f , get %f , delete %f", $t2 - $t1, $t3 - $t2, $t4 - $t3), PHP_EOL;
}

function shmop_set($key, $value)
{ }


function shmop_get($key, $value)
{ }


// 优势加锁后单台多程序并发安全
function shmop_test()
{
    $arr = [
        ['id' => 0, 'username' => 'hello', 'password' => '6789', 'age' => 20],
        ['id' => 1, 'username' => 'world', 'password' => '1234', 'age' => 22],
    ];
    $str = json_encode($arr);

    $key = ftok(__FILE__, 'h'); // 只能是存在的文件转为数字
    $size = strlen($str);

    if (false) {
        $t1 = microtime(true);
        $signal = sem_get($key); // 请求信号控制权
        if (sem_acquire($signal)) { //获取一把锁 
            $shmid = shmop_open($key, 'c', 0644, $size);
            for ($i = 0; $i < RUN_TIMES; $i++) {
                shmop_write($shmid, $str, 0);
                // $read = shmop_read($shmid, 0, $size);
            }
            shmop_close($shmid);
        }
        sem_release($signal); //释放信号  开门
        $t2 = microtime(true);
    } else {
        $t1 = microtime(true);
        $signal = sem_get($key); // 请求信号控制权
        if (sem_acquire($signal)) { //获取一把锁 
            for ($i = 0; $i < RUN_TIMES; $i++) {
                $shmid = shmop_open($key, 'c', 0644, $size);
                shmop_write($shmid, $str, 0);
                // $read = shmop_read($shmid, 0, $size);
                shmop_close($shmid);
            }
        }
        sem_release($signal); //释放信号  开门
        $t2 = microtime(true);
    }


    echo sprintf("shmop : set %f ", $t2 - $t1), PHP_EOL;
}



function shm_put_test()
{

    $arr = [
        ['id' => 0, 'username' => 'hello', 'password' => '6789', 'age' => 20],
        ['id' => 1, 'username' => 'world', 'password' => '1234', 'age' => 22],
    ];
    $str = json_encode($arr);

    $key = 0x4337b124;
    // 创建一个共享内存
    $shm_id = shm_attach($key, RUN_TIMES * 512, 0666); // resource type
    if ($shm_id === false) {
        die('Unable to create the shared memory segment' . PHP_EOL);
    }

    $t1 = microtime(true);
    for ($i = 0; $i < RUN_TIMES; $i++) {
        shm_put_var($shm_id, $i, $str);
    }
    $t2 = microtime(true);
    for ($i = 0; $i < RUN_TIMES; $i++) {
        shm_get_var($shm_id, $i);
    }
    $t3 = microtime(true);
    for ($i = 0; $i < RUN_TIMES; $i++) {
        shm_remove_var($shm_id, $i);
    }
    $t4 = microtime(true);

    //从系统中移除
    shm_remove($shm_id);

    //关闭和共享内存的连接
    shm_detach($shm_id);
    echo sprintf("shm_put : set %f , get %f , delete %f", $t2 - $t1, $t3 - $t2, $t4 - $t3), PHP_EOL;
}


//基于/dev/shm的缓存实现的性能测试，基于内存的缓存，在Windows上可以用普通文件系统做测试。

function shm_test()
{

    $arr = [
        ['id' => 0, 'username' => 'hello', 'password' => '6789', 'age' => 20],
        ['id' => 1, 'username' => 'world', 'password' => '1234', 'age' => 22],
    ];
    $str = json_encode($arr);


    $t1 = microtime(true);
    for ($i = 0; $i < RUN_TIMES; $i++) {
        file_put_contents("/dev/shm/key$i", $str, LOCK_EX);
    }
    $t2 = microtime(true);
    for ($i = 0; $i < RUN_TIMES; $i++) {
        file_get_contents("/dev/shm/key$i", false);
    }
    $t3 = microtime(true);
    for ($i = 0; $i < RUN_TIMES; $i++) {
        unlink("/dev/shm/key$i");
    }
    $t4 = microtime(true);

    echo sprintf("shm file : set %f , get %f , delete %f", $t2 - $t1, $t3 - $t2, $t4 - $t3), PHP_EOL;
}


redis_test();
swoole_test();
shm_test();
shm_put_test();
shmop_test();
