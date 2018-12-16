<?php
	
	namespace Vendor\Database;
	use PDO;

	class Mysql
	{
		protected static $config = null;
		protected static $db = null;
		public static $lastSql = [];
		public static $sqlStop = false;
		protected $sql;
		protected $result;
		protected $prefix;
		protected $where;
		protected $connect;
		protected $limit;
		protected $fields;
		protected $table;
		protected $alias;
		protected $join;
		protected $orderBy;
		protected $groupBy;
		protected $addFrefix = false;

		public function __construct($table = null)
		{
			$this->handelConfig($table);
		}

		private function handelConfig($table)
		{
			if (Mysql::$config === null) {
				Mysql::$config = require_once('./config/Database.php');
			}
			
			$config = Mysql::$config;
			$this->prefix = $config['prefix'];
			$this->table = $table;
			if (Mysql::$db === null) {
				$optsValues = [PDO::ATTR_PERSISTENT=>true, PDO::ATTR_ERRMODE=>2, PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES utf8'];
				$db = new PDO("mysql:host={$config['host']};dbname={$config['database']};port={$config['port']};", $config['username'], $config['password'], $optsValues); 

                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
                $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $db->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);

                Mysql::$db = $db;
			}
		}

		private function handleFields($fields)
		{
			if (is_string($fields)) {
				$fields = explode(',', $fields);
			}

			foreach ($fields as &$field) {
				if (strpos($field, '.')) {
					$name = explode('.', trim($field));
					$field = $this->addFrefix($name[0] . '.`' . $name[1] . '`');
				} else {
					$field = '`' . trim($field) . '`';
				}
			}
			return implode(',', $fields);
		}

		public function alias($alias)
		{
			$this->alias = $alias;
			return $this;
		}

		private function getAlias()
		{
			return  ' ' . $this->alias . ' ' ?: '';
		}

		public function select($select)
		{
			$this->fields = $this->handleFields($select);
			return $this;
		}

		public function where($key, $symbol = null, $value = null,  $type = false)
		{
			$where = '';
            if (is_string($key) && !isset($symbol) && !isset($value)) {
                $where .= $key;
            } elseif (is_string($key) && isset($symbol) && !isset($value)) {
                $where .= $this->handleFields($key) . '="' . $symbol . '"';
            } elseif (is_string($key) && isset($symbol) && isset($value)) {
                $where .= $this->handleFields($key) . ' ' . $symbol . ' "' . $value . '"';
            } elseif (is_array($key) && !isset($symbol) && !isset($value)) {
                foreach ($key as $k => $v) {
                    $where .= $this->handleFields($k) . '="' . $v . '" AND ';   
                }
                $where = rtrim($where, 'AND ');
            }

            $condition = $this->where;

            if (! empty($type)) {
                $condition .= ' ' . $type . ' ' . $where;
            } elseif (! empty($condition)) {
                $condition .= ' AND '. $where;
            } else {
                $condition = ' WHERE ' . $where;
            }

            $this->where = $condition;
          
            return $this;
		}

		public function orWhere($key, $symbol = null, $value = null)
		{
			$this->where($key, $symbol, $value, 'OR');

			return $this;
		}

		protected function whereInHandle($key, $value, $boolean = 'IN')
        {
            if (is_array($value)) {
                $str = '';
                foreach ($value as $val) {
                    $str .= '"'.trim($val).'",';
                }

                $value = trim($str, ',');
            }   

            return $key . ' ' . $boolean . ' ('.$value.')';
        }

        public function whereIn($key, $value)
        {
            return $this->where($this->whereInHandle($key, $value));
        }

		protected function getWhere()
		{
			return $this->where ?: '';
		}

		protected function getFields()
		{
			return $this->fields ?: '*';
		}

		private function addFrefix($field)
		{
			if (! $this->alias && $this->prefix && strpos($field, $this->prefix) === false) {
				return $this->prefix . $field;
			}
			return $field;
		}

		protected function getTable($table)
		{
			return $this->prefix . $table;
		}

		public function leftJoin($table, $field1, $condition, $field2)
		{	
			$this->join .= 'LEFT JOIN ' . $this->getTable($table) . ' ON ' .  $this->handleFields($field1) . ' ' . $condition . ' ' . $this->handleFields($field2);
			return $this;
		}

		public function joinRaw($join)
		{
			$this->join .= $join;
			return $this;
		}

		private function getJoin()
		{
			return $this->join ?: '';
		}

		public function orderBy($field, $order = null)
		{
			if ($order === null) {
				$this->orderBy = ' ORDER BY ' . $this->handleFields($field);
			} else  {
				$this->orderBy = ' ORDER BY ' . $this->handleFields($field) . ' ' . $order;
			}
			return $this;
		}

		private function getOrderBy()
		{
			return $this->orderBy ?: '';
		}

		public function groupBy($group)
		{
			$this->groupBy = $group;
			return $this;
		}

		private function getGroup()
		{
			return $this->groupBy ?: '';
		}

		public function first()
		{
			$data = $this->getRow($this->getRowSql() . ' LIMIT 1');

			if (! empty($data)) {
				return $data[0];
			}
			return $data;
		}

		public function value($value)
		{
			$this->select($value);

			$data = $this->first();

			if (strpos($value, '.')) {
                $value = explode('.', $value);
                $value = $value[1];
            }

            if (empty($data)) {
                return '';
            }

            return $data->$value;
		}

		private function getRowSql()
		{
			return str_replace('  ', '','SELECT ' . $this->getFields() . ' FROM ' . $this->getTable($this->table) . $this->getAlias() . $this->getJoin()  . $this->getWhere() . $this->getOrderBy() . $this->getGroup() . $this->getLimit() );
		}

		public function limit($parameter1 = 8, $parameter2 = null)
		{
			if (empty($parameter2)) {
                $limit = $parameter1;
            } else {
                $limit = $parameter1 . ',' . $parameter2;
            }
            $this->limit = ' LIMIT ' . $limit;
            return $this;
		}

		private function getLimit()
		{
			return $this->limit ?: '';
		}

		public function get()
		{
            return $this->getRow($this->getRowSql());
		}

		public function count()
		{
			$sql = 'SELECT COUNT(' . $this->getFields() . ') AS NUM FROM ' . $this->getTable($this->table) . $this->getAlias() . $this->getJoin() . $this->getWhere();

			$count = $this->getRow($sql);

			return $count[0]->NUM;
		}

        public function insert(array $data)
        {  
            if (count($data) == count($data, 1)) {
                $type = 'insert';
                $data = $this->hendleInsertArray($data);
                $fields = $data[0];
                $values = $data[1];
            } else {
                $type = false;
                $fields = '';
                $values = '';
                foreach ($data as $key => $val) {
                    $res = $this->hendleInsertArray($val);
                    $fields = $res[0];
                    $values .= $res[1] . ',';
                }
                $values = trim($values, ',');         
            }

            $sql = 'INSERT INTO ' . $this->getTable($this->table) . ' (' . $fields . ') VALUES ' . $values;

            return $this->updateQuery($sql, $type);
        }

        private function hendleInsertArray($data)
        {
              $fields = '';
              $values = '';
              foreach ($data as $key => $val) {
                    $fields .= '`' . $key . '`,';
                    $values .= '"' . addslashes($val) . '",';
                }
                $fields = trim($fields, ',');
                $values = '(' . trim($values, ',') . ')'; 

            return [$fields, $values]; 
        }


        public function save(array $data)
        {
            if (empty($this->where)) {
                return false;
            }

            $fields = '';
            foreach ($data as $key => $val) {
                $fields .= "`" . $key . "`='" . addslashes($val) . "',";
            }
            $fields = trim($fields, ',');

            $sql = 'UPDATE ' . $this->getTable($this->table);

            if ($leftJoin =  $this->getJoin()) {
                $sql .= $leftJoin;
            }

            $sql .= ' SET ' . $fields . $this->getWhere();

            return $this->updateQuery($sql);
        }

        public function delete()
        {
        	if (empty($this->where)) {
                return false;
            }

            $sql = 'DELETE FROM ' . $this->getTable($this->table) . $this->getWhere();

            return $this->updateQuery($sql);
        }

        public function exist()
        {
        	if ($this->count())
        	{
        		return true;
        	}

        	return false;
        }

        public function calculate($field, $calculate = null)
        {
        	if (!empty($calculate)) {
                $field = $field . '=' . $field . '+' . $calculate;
            } elseif (!empty($field) && is_array($field)) {
                $fields = '';
                foreach ($field as $key => $val) {
                    $fields .= $key . '=' . $key . '+' . $val . ','; 
                }
                $field = trim($fields, ',');
            }

            if (empty($this->where) || empty($field)) {
                return false;
            }

            $sql = 'UPDATE ' . $this->getTable($this->table) . ' SET ' . $field . $this->getWhere();

            return $this->updateQuery($sql);
        }

		public function getRow($sql)
		{
			$smart = $this->query($sql);

			if ($smart === false)
			{
				return $smart;
			}

			$data = [];
			while ($result = $smart->fetch(PDO::FETCH_OBJ)) {
                $data[] = $result;
            }

            return $data;
		}

		public function query($sql)
		{
			Mysql::$lastSql[] = $sql;

			if (Mysql::$sqlStop)
			{
				return false;
			}

			$smart = Mysql::$db->prepare($sql);

			$smart->execute();

			return $smart;
		}

		private function updateQuery($sql, $type = 'update')
		{
			$smart = $this->query($sql);

			if (Mysql::$sqlStop)
			{
				return false;
			}

			if ($type != 'update') 
			{
				return Mysql::$db->lastInsertId();
			}

			return $smart->rowCount();
		}

		private function release()
		{
			$this->sql = null;
			$this->where = null;
			$this->limit = null;
			$this->fields = null;
			$this->alias = null;
			$this->join = null;
			$this->orderBy = null;
			$this->groupBy = null;
		}
	}















