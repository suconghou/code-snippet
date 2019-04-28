<?php
ob_end_clean();

function doSomething(){
    file_get_contents('https://github.com');
    file_get_contents('https://developer.github.com/');
    file_get_contents('https://golang.google.cn/');
}

$key = ftok(__FILE__, 't');

$semaphore = sem_get($key, 1);
if (sem_acquire($semaphore, true) !== false) {
    echo "I can do something",PHP_EOL ;
    doSomething() ;
    sem_release($semaphore) ;
} else {
    echo " Another process is running",PHP_EOL ;
}