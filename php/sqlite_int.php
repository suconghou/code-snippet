<?php

/**
 * Kvdb 小数据持久化存储
 */

class Kvdb
{
	const tCache = '`kvdb`';
	private static $instance;

	public static function ready(string $file = '/tmp/1.db')
	{
		if (!self::$instance) {
			self::$instance = new SQLite3($file);
			foreach (['PRAGMA JOURNAL_MODE=OFF', 'PRAGMA LOCKING_MODE=EXCLUSIVE', 'PRAGMA SYNCHRONOUS=OFF', 'PRAGMA CACHE_SIZE=8000', 'PRAGMA PAGE_SIZE=4096', 'PRAGMA TEMP_STORE=MEMORY'] as $sql) {
				self::$instance->exec($sql);
			}
		}
		return self::$instance;
	}

	public static function get(string $key, $default = null, $decode = true)
	{
		$value = self::ready()->query('SELECT * FROM microfed_app');
		var_dump($value->fetchArray(SQLITE3_ASSOC));
	}
}

Kvdb::get('');


// 使用SQLite3时int数据正常,使用PDO时,int数据自动被转化为了string类型
// 对于大部分数据库,PDO都有此问题.
// 在使用mysqlnd驱动时,设置ATTR_EMULATE_PREPARES:false,ATTR_STRINGIFY_FETCHES:false 可以解决mysql数据库类型问题
// sqlite3 pdo 此问题仍无法解决
