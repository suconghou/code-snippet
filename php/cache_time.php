<?php

$dir = '../var/html/';
$time = time();
$dirObj = new RegexIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)), '/^[\w\/\-\\\.:]+\.gz$/i');
foreach ($dirObj as $file => $fileinfo) {
    $t = filemtime($file);
    $status = 'ok';
    if ($time - $t > 5184000) {
        unlink($file);
        $status = 'deleted';
    }
    echo $file, ':', $t, ' ', $status, PHP_EOL;
}
