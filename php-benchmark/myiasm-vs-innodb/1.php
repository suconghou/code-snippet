<?php

/**
 * Kvdb 小数据持久化存储
 */

class Kvdb
{
	const tCache='`kvdb`';
	private static $instance;

	public static function ready(string $file='kvdb.db')
	{
		if(!self::$instance)
		{
			self::$instance=new SQLite3($file);
			foreach (['PRAGMA JOURNAL_MODE=OFF', 'PRAGMA LOCKING_MODE=EXCLUSIVE', 'PRAGMA SYNCHRONOUS=OFF', 'PRAGMA CACHE_SIZE=8000', 'PRAGMA PAGE_SIZE=4096', 'PRAGMA TEMP_STORE=MEMORY','CREATE TABLE IF NOT EXISTS '.self::tCache.' ("k" text NOT NULL, "v" text NOT NULL, "t" integer NOT NULL, PRIMARY KEY ("k") )'] as $sql)
			{
				self::$instance->exec($sql);
			}
		}
		return self::$instance;
	}

	public static function set(string $key,$value,int $expired=86400,$encode=true)
	{
		$t=$expired>2592000?$expired:time()+$expired;
		$stm=self::ready()->prepare('REPLACE INTO '.self::tCache." (k,v,t) VALUES ('$key',:v,$t)");
		$stm->bindValue(':v',$encode?json_encode($value,JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES):$value);
		return (bool)$stm->execute();
	}


	public static function mset(array $set,int $expired=86400,$encode=true)
	{
		$i=0;
		$t=$expired>2592000?$expired:time()+$expired;
		$holders=array_map(function($k)use($t){return "('{$k}',?,{$t})";},array_keys($set));
		$stm=self::ready()->prepare('REPLACE INTO '.self::tCache.' (k,v,t) VALUES '.implode(',',$holders));
		array_walk($set,function($v)use(&$stm,&$i,$encode){$stm->bindValue(++$i,$encode?json_encode($v,JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES):$v);});
		return (bool)$stm->execute();
	}

	public static function get(string $key,$default=null,$decode=true)
	{
		$value=self::ready()->querySingle('SELECT v FROM '.self::tCache." WHERE k='{$key}' and t > (SELECT strftime('%s', 'now')) ");
		return $value?($decode?json_decode($value,true,32,JSON_THROW_ON_ERROR):$value):$default;
	}

	public static function mget(array $keys=null,$decode=true)
	{
		$i=0;
		if($keys)
		{
			$stm=self::ready()->prepare('SELECT k,v FROM '.self::tCache.' WHERE k IN ('.implode(',',array_fill(0,count($keys),'?')).") AND t > (SELECT strftime('%s', 'now')) ");
			array_walk($keys,function($v)use(&$stm,&$i){$stm->bindValue(++$i,$v);});
			$res=$stm->execute();
			$result=[];
			while($tmp=$res->fetchArray(SQLITE3_ASSOC))
			{
				$result[$tmp['k']]=$decode?json_decode($tmp['v'],true,32,JSON_THROW_ON_ERROR):$tmp['v'];
			}
			foreach (array_diff($keys,array_keys($result)) as $k)
			{
				$result[$k]=null;
			}
			return $result;
		}
		else
		{
			$res=self::ready()->query('SELECT k,v FROM '.self::tCache." WHERE t > (SELECT strftime('%s', 'now')) ");
			$result=[];
			while($tmp=$res->fetchArray(SQLITE3_ASSOC))
			{
				$result[$tmp['k']]=json_decode($tmp['v'],true,32,JSON_THROW_ON_ERROR);
			}
			return $result;
		}
	}

	public static function ex(string $key,int $expired=86400)
	{
		$t=$expired>2592000?$expired:time()+$expired;
		$stm=self::ready()->prepare('UPDATE '.self::tCache." SET t={$t} WHERE k=:k");
		$stm->bindValue(':k',$key);
		return (bool)$stm->execute();
	}

	public static function clear($key=[],$i=0)
	{
		if($key)
		{
			$stm=self::ready()->prepare('DELETE FROM '.self::tCache.(is_array($key)?(" WHERE k IN (".implode(',',array_fill(0,count($key),'?')).") "):null));
			is_array($key)&&array_walk($key,function($v)use(&$stm,&$i){$stm->bindValue(++$i,$v);});
			return (bool)$stm->execute();
		}
		return self::ready()->exec('DELETE FROM '.self::tCache." WHERE t < (SELECT strftime('%s', 'now'))");
	}
}





function test_sqlite()
{
    $table = 'cache';
    $instance = new SQLite3('/dev/shm/1.db');
    foreach (['PRAGMA JOURNAL_MODE=OFF', 'PRAGMA LOCKING_MODE=EXCLUSIVE', 'PRAGMA SYNCHRONOUS=OFF', 'PRAGMA CACHE_SIZE=8000', 'PRAGMA PAGE_SIZE=4096', 'PRAGMA TEMP_STORE=MEMORY', 'CREATE TABLE IF NOT EXISTS ' . $table . ' ("k" text NOT NULL, "v" text NOT NULL, "t" integer NOT NULL, PRIMARY KEY ("k") )'] as $sql) {
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
        $instance->exec('UPDATE ' . $table . " SET v='$t' WHERE k='key{$i}' ");
    }
    $t4 = microtime(true);
    for ($i = 0; $i < 5e2; $i++) {
        $instance->exec('DELETE FROM ' . $table . " WHERE k='key{$i}' ");
    }
    $t5 = microtime(true);
    echo sprintf("sqlite : insert %f , select %f , update %f, delete %f", $t2 - $t1, $t3 - $t2, $t4 - $t3, $t5 - $t4), PHP_EOL;
}


function test_sqlite2()
{
    Kvdb::ready("/dev/shm/1.db");
    $t = time() + 3600;
    $t1 = microtime(true);
    for ($i = 0; $i < 5e2; $i++) {
        Kvdb::set('t'.$i,$i,3600,false);
    }
    $t2 = microtime(true);
    for ($i = 0; $i < 5e2; $i++) {
        Kvdb::get('t'.$i,null,false);
    }
    $t3 = microtime(true);
    for ($i = 0; $i < 5e2; $i++) {
        Kvdb::set('t'.$i,$i,3600,false);
    }
    $t4 = microtime(true);
    for ($i = 0; $i < 5e2; $i++) {
        Kvdb::clear('t'.$i);
    }
    $t5 = microtime(true);
    echo sprintf("sqlite : insert %f , select %f , update %f, delete %f", $t2 - $t1, $t3 - $t2, $t4 - $t3, $t5 - $t4), PHP_EOL;
}

test_sqlite();

test_sqlite2();