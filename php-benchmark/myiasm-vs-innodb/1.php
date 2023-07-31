<?php

/**
 * Kvdb 小数据持久化存储
 */

class Kvdb
{
	private const tCache = '`kvdb`';

	public static function ready(string $file = 'kvdb.db'): SQLite3
	{
		static $_db;
		if (!$_db) {
			$_db = new SQLite3($file);
			foreach (['PRAGMA JOURNAL_MODE=MEMORY', 'PRAGMA LOCKING_MODE=EXCLUSIVE', 'PRAGMA SYNCHRONOUS=OFF', 'PRAGMA CACHE_SIZE=8192', 'PRAGMA PAGE_SIZE=4096', 'PRAGMA TEMP_STORE=MEMORY', 'CREATE TABLE IF NOT EXISTS ' . self::tCache . ' (k TEXT PRIMARY KEY, v TEXT NOT NULL, a INTEGER NOT NULL, t INTEGER NOT NULL) WITHOUT ROWID'] as $sql) {
				$_db->exec($sql);
			}
		}
		return $_db;
	}

	public static function set(string $k, array|string|int|float|bool $v, int $t): bool
	{
		$a = is_array($v) ? 1 : (is_string($v) ? 2 : (is_int($v) ? 3 : (is_float($v) ? 4 : (is_bool($v) ? 5 : 6))));
		$stm = self::ready()->prepare('REPLACE INTO ' . self::tCache . " (k,v,a,t) VALUES (:k,:v,$a,$t)");
		$stm->bindValue(':k', $k);
		$stm->bindValue(':v', $a == 1 ? json_encode($v, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $v);
		return (bool)$stm->execute();
	}


	public static function mset(array $data, int $t): bool
	{
		$i = 0;
		$holders = [];
		$params = [];
		foreach ($data as $k => $v) {
			$a = is_array($v) ? 1 : (is_string($v) ? 2 : (is_int($v) ? 3 : (is_float($v) ? 4 : (is_bool($v) ? 5 : 6))));
			$_k = ":k_{$i}";
			$_v = ":v_{$i}";
			$holders[] = "($_k,$_v,$a,$t)";
			$params[$_k] = $k;
			$params[$_v] = $a == 1 ? json_encode($v, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $v;
			$i++;
		}
		$stm = self::ready()->prepare('REPLACE INTO ' . self::tCache . ' (k,v,a,t) VALUES ' . implode(',', $holders));
		foreach ($params as $k => $v) {
			$stm->bindValue($k, $v);
		}
		return (bool)$stm->execute();
	}

	public static function get(string $k, array|string|int|float|bool $default = null): array|string|int|float|bool|null
	{
		$stm = self::ready()->prepare('SELECT v,a FROM ' . self::tCache . " WHERE k=:k and t > (SELECT strftime('%s', 'now')) ");
		$stm->bindValue(':k', $k);
		$res = $stm->execute()->fetchArray(SQLITE3_NUM);
		if (!$res) {
			return $default;
		}
		[$v, $a] = $res;
		return match ($a) {
			1 => json_decode($v, true, 32, JSON_THROW_ON_ERROR),
			2 => strval($v),
			3 => intval($v),
			4 => floatval($v),
			5 => boolval($v),
		};
	}

	public static function mget(array $keys = [], array|string|int|float|bool $default = null): array
	{
		$w = $keys ? ('k IN (' . implode(',', array_fill(0, count($keys), '?')) . ') AND') : '';
		$stm = self::ready()->prepare('SELECT k,v,a FROM ' . self::tCache . " WHERE {$w} t > (SELECT strftime('%s', 'now'))");
		$i = 0;
		foreach ($keys as $k) {
			$stm->bindValue(++$i, $k);
		}
		$res = $stm->execute();
		$result = [];
		while ($t = $res->fetchArray(SQLITE3_NUM)) {
			[$k, $v, $a] = $t;
			$result[$k] = match ($a) {
				1 => json_decode($v, true, 32, JSON_THROW_ON_ERROR),
				2 => strval($v),
				3 => intval($v),
				4 => floatval($v),
				5 => boolval($v),
			};
		}
		foreach (array_diff($keys, array_keys($result)) as $k) {
			$result[$k] = $default;
		}
		return $result;
	}

	public static function ex(string $k, int $t): bool
	{
		$stm = self::ready()->prepare('UPDATE ' . self::tCache . " SET t={$t} WHERE k=:k");
		$stm->bindValue(':k', $k);
		return (bool)$stm->execute();
	}

	public static function clear(array|string|bool $key = []): bool
	{
		if (is_array($key) || is_string($key)) {
			if (!is_array($key)) {
				$key = [$key];
			}
			$stm = self::ready()->prepare('DELETE FROM ' . self::tCache . " WHERE k IN (" . implode(',', array_fill(0, count($key), '?')) . ')');
			$i = 0;
			foreach ($key as $k) {
				$stm->bindValue(++$i, $k);
			}
			return (bool)$stm->execute();
		}
		return self::ready()->exec('DELETE FROM ' . self::tCache . ($key ? '' : " WHERE t < (SELECT strftime('%s', 'now'))"));
	}
}




function test_sqlite()
{
	$table = 'cache';
	$instance = new SQLite3('/tmp/1.db');
	foreach (['PRAGMA JOURNAL_MODE=OFF', 'PRAGMA LOCKING_MODE=EXCLUSIVE', 'PRAGMA SYNCHRONOUS=OFF', 'PRAGMA CACHE_SIZE=8000', 'PRAGMA PAGE_SIZE=4096', 'PRAGMA TEMP_STORE=MEMORY', 'CREATE TABLE IF NOT EXISTS ' . $table . ' ("k" text NOT NULL, "v" text NOT NULL, "t" integer NOT NULL, PRIMARY KEY ("k") )'] as $sql) {
		$instance->exec($sql);
	}
	$t = time() + 3600;
	$t1 = microtime(true);
	for ($i = 0; $i < 5e4; $i++) {
		$stm = $instance->prepare('INSERT INTO ' . $table . " (k,v,t) VALUES ('key$i',:v,$t)");
		$stm->bindValue(':v', $i);
		$stm->execute();
	}
	$t2 = microtime(true);
	for ($i = 0; $i < 5e4; $i++) {
		$instance->querySingle('SELECT v FROM ' . $table . " WHERE k='key{$i}' ");
	}
	$t3 = microtime(true);
	for ($i = 0; $i < 5e4; $i++) {
		$instance->exec('UPDATE ' . $table . " SET v='$t' WHERE k='key{$i}' ");
	}
	$t4 = microtime(true);
	for ($i = 0; $i < 5e4; $i++) {
		$instance->exec('DELETE FROM ' . $table . " WHERE k='key{$i}' ");
	}
	$t5 = microtime(true);
	echo sprintf("sqlite : insert %f , select %f , update %f, delete %f", $t2 - $t1, $t3 - $t2, $t4 - $t3, $t5 - $t4), PHP_EOL;
}


function test_sqlite2()
{
	Kvdb::ready("/tmp/1.db");
	$t = time() + 3600;
	$t1 = microtime(true);
	for ($i = 0; $i < 5e4; $i++) {
		Kvdb::set('t' . $i, $i, 3600);
	}
	$t2 = microtime(true);
	for ($i = 0; $i < 5e4; $i++) {
		Kvdb::get('t' . $i, null);
	}
	$t3 = microtime(true);
	for ($i = 0; $i < 5e4; $i++) {
		Kvdb::set('t' . $i, $i, 3600);
	}
	$t4 = microtime(true);
	for ($i = 0; $i < 5e4; $i++) {
		Kvdb::clear('t' . $i);
	}
	$t5 = microtime(true);
	echo sprintf("sqlite : insert %f , select %f , update %f, delete %f", $t2 - $t1, $t3 - $t2, $t4 - $t3, $t5 - $t4), PHP_EOL;
}

test_sqlite();

test_sqlite2();
