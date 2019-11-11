<?php


$sql1 = "CREATE TABLE `test_1` (`k` varchar(100) NOT NULL, `v` text NOT NULL, `t` int NOT NULL, PRIMARY KEY (`k`) ) ENGINE='MyISAM' COLLATE 'utf8mb4_general_ci';";
$sql2 = "CREATE TABLE `test_2` (`k` varchar(100) NOT NULL, `v` text NOT NULL, `t` int NOT NULL, PRIMARY KEY (`k`) ) ENGINE='InnoDB' COLLATE 'utf8mb4_general_ci';";
$sql3 = "CREATE TABLE `test_3` (`k` varchar(100) NOT NULL, `v` varchar(5000) NOT NULL, `t` int NOT NULL, PRIMARY KEY (`k`) ) ENGINE='MEMORY' COLLATE 'utf8mb4_general_ci';";


$dbDsn = "mysql:host=127.0.0.1;port=13306;dbname=test;charset=utf8";
$dbUser = "work";
$dbPass = "123456";

$options = [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_TIMEOUT => 3, PDO::ATTR_EMULATE_PREPARES => false];

function test_table($pdo, $table)
{
    $t = time();
    $t1 = microtime(true);
    for ($i = 0; $i < 5e2; $i++) {
        $pdo->exec("INSERT INTO $table VALUES($i,$i,$t)");
    }
    $t2 = microtime(true);
    for ($i = 0; $i < 5e2; $i++) {
        $pdo->query("SELECT v FROM $table WHERE k ='$i' ")->fetch();
    }
    $t3 = microtime(true);
    for ($i = 0; $i < 5e2; $i++) {
        $pdo->exec("UPDATE $table SET v='$t' WHERE k ='$i' ");
    }
    $t4 = microtime(true);
    for ($i = 0; $i < 5e2; $i++) {
        $pdo->exec("DELETE FROM $table WHERE k ='$i' ");
    }
    $t5 = microtime(true);

    echo sprintf("$table : insert %f , select %f , update %f, delete %f", $t2 - $t1, $t3 - $t2, $t4 - $t3, $t5 - $t4), PHP_EOL;
}


function test_sqlite()
{
    $table = 'cache';
    $instance = new SQLite3('/dev/shm/1.db');
    foreach (['PRAGMA SYNCHRONOUS=OFF', 'PRAGMA CACHE_SIZE=50000', 'PRAGMA TEMP_STORE=MEMORY', 'CREATE TABLE IF NOT EXISTS ' . $table . ' ("k" text NOT NULL, "v" text NOT NULL, "t" integer NOT NULL, PRIMARY KEY ("k") )'] as $sql) {
        $instance->exec($sql);
    }
    $t = time() + 3600;
    $t1 = microtime(true);
    for ($i = 0; $i < 5e2; $i++) {
        $stm = $instance->prepare('INSERT INTO ' . $table . " (k,v,t) VALUES ('key$i',:v,$t)");
        $stm->bindValue(':v', $i);
        $stm->execute();
    }
    $t2 = microtime(true);
    for ($i = 0; $i < 5e2; $i++) {
        $instance->querySingle('SELECT v FROM ' . $table . " WHERE k='key{$i}' ");
    }
    $t3 = microtime(true);
    for ($i = 0; $i < 5e2; $i++) {
        $instance->exec('UPDATE '.$table." SET v='$t' WHERE k='key{$i}' ");
    }
    $t4 = microtime(true);
    for ($i = 0; $i < 5e2; $i++) {
        $instance->exec('DELETE FROM '.$table." WHERE k='key{$i}' ");
    }
    $t5 = microtime(true);
    echo sprintf("sqlite : insert %f , select %f , update %f, delete %f", $t2 - $t1, $t3 - $t2, $t4 - $t3, $t5 - $t4), PHP_EOL;

}

try {
    $pdo = new PDO($dbDsn, $dbUser, $dbPass, $options);

    for ($i = 1; $i <= 3; $i++) {
        $pdo->exec("DROP TABLE IF EXISTS `test_$i` ");
    }
    $pdo->exec($sql1);
    $pdo->exec($sql2);
    $pdo->exec($sql3);
    test_table($pdo, 'test_1');
    test_table($pdo, 'test_2');
    test_table($pdo, 'test_3');
    test_sqlite();
} catch (PDOException $e) {
    print_r($e);
    die;
}
