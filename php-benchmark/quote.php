<?php
	error_reporting(E_ALL & ~E_NOTICE);

	echo PHP_VERSION,"\n";

    $t1_start = number_format(microtime(true),10,'.','');
    $array_1 = array();
    for($i=0;$i<=10000;$i++){
        $array_1[test] = 1;
    }
    $t1_end = number_format(microtime(true),10,'.','');
    echo '无引号: ' , $t1_end - $t1_start , "\n";

    $t2_start = number_format(microtime(true),10,'.','');
    $array_2 = array();
    for($i=0;$i<=10000;$i++){
        $array_2['test'] = 1;
    }
    $t2_end = number_format(microtime(true),10,'.','');
    echo '单引号: ' , $t2_end - $t2_start,"\n";

    $t3_start = number_format(microtime(true),10,'.','');
    $array_3 = array();
    for($i=0;$i<=10000;$i++){
        $array_3["test"] = 1;
    }
    $t3_end = number_format(microtime(true),10,'.','');
    echo '双引号: ' , $t3_end - $t3_start,"\n";
