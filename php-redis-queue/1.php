<?php 

$a=file_get_contents("http://www.workerman.net/check.php");
file_put_contents("a.php",$a);
