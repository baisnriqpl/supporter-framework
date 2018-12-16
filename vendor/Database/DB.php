<?php

	namespace Vendor\Database;

	use Vendor\Database\Mysql;

	class DB
	{
		protected $mysql;

		public function __construct()
		{
			$this->mysql = new Mysql();
		}

		public function table($table)
		{
			return new Mysql($table);
		}

		public function query($sql)
		{
			return $this->mysql->getRow($sql);
		}

		public function stopSql()
		{
			$this->mysql::$sqlStop = true;
		}

		public function lastSql()
		{
			return $this->mysql::$lastSql;
		}
	}