<?php

	namespace Vendor\Database;
	use Vendor\Database\Mysql;
	use Vendor\Database\Collection;
	use Vendor\Http\Request;
	use stdClass;

	class Builder 
	{
		public $attributes = [];
		private $result;
		public $appends = [];
		protected $db;

		public function __construct()
		{
			$this->db = new Mysql($this->getTable());
		}

		protected function getTable()
		{
			$class = explode('\\', get_class($this));
			$table = lcfirst($class[count($class) -1]);

			return $this->table ?: lcfirst($table);
		}

		public function querySelect($field)
		{
			$this->db->select($field);
			return $this;
		}

		public function queryWhere($column, $operator = null, $value = null, $type = null)
		{
			$this->db->where($column, $operator, $value, $type);
			return $this;
		}

		public function queryWhereIn($key, $value)
		{
			$this->db->whereIn($key, $value);
			return $this;
		}

		public function queryOrWhere($column, $operator = null, $value = null)
		{
			$this->db->orWhere($column, $operator, $value);
			return $this;
		}

		protected function queryWhereInHandle($key, $value, $boolean = 'IN')
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

        public function queryWhereNotIn($key, $value)
        {   
            return $this->queryWhere($this->queryWhereInHandle($key, $value, 'NOT IN'));
        }

		public function queryOrderBy($field, $order = null)
		{
			$this->db->orderBy($field, $order);
			return $this;
		}

		public function queryFirst()
		{
			$data = $this->db->first();
			return $this->getFirst($data);
		}

		private function getFirst($data)
		{
			$this->attributes = $this->addAttributes($data);
			return $this;
		} 

		public function queryValue($key)
		{
			return $this->db->value($key);
		}

		public function queryLimit($parameter1 = 8, $parameter2 = null)
		{
			return $this->builder($this->db->limit($parameter1, $parameter2)->get());
		}

		public function queryCount()
		{
			return $this->db->count();
		}

		public function queryExist()
		{
			return $this->db->exist();
		}

		public function queryPaginate($per_page = 8)
        {
            $page = request()->all('page');
            $page = $page ? $page : 1;
            $perPage = request()->all('per_page');
            $perPage = $perPage ?: $per_page;
            $limit = ($page - 1) * $perPage . ',' . $perPage;
            $all = $this->queryCount();
            $allPage = ceil($all / $per_page);
            $page = $page >= $allPage ? $allPage : $page;
            $next = $page == $allPage ? $page : $page + 1;
            $result = $this->queryLimit($limit)->toArray();

            $data = [
                        'current_page'      =>  $page,
                        'per_page'          =>  $perPage,
                        'total'             =>  $all,
                        'all_page'			=>	$allPage,
                        'last'				=>	$page - 1 ?: 1,
                        'next'				=>	$next,
                        'data'              =>  $result
                    ];
            $this->result = $data;

            return $data;
        }

		public function queryGet()
		{
			return $this->builder($this->db->get());
		}

		public function queryInsert($data)
		{
			return $this->db->insert($data);
		}

		public function queryDelete()
		{
			return $this->db->delete();
		}

		public function querySave($data)
		{
			return $this->db->save($data);
		}

		public function queryCalculate($field, $calculate = null)
		{
			return $this->db->calculate($field, $calculate);
		}

		protected function addAttributes($data)
        {
            if (! empty($this->appends) && is_array($this->appends) && ! empty($data)) {
                $appends = $this->appends;

                $attributes = [];
                foreach ($data as $key => $val) {
                	$attributes[$key] = $val;
                } 

                $this->attributes = $attributes;

                foreach ($appends as $k => $v) {
                	$newAttribute = $this->getAttribute($v);
	                if ($newAttribute !== null) {
	                    $data->$v = $newAttribute;
	                }
                } 
            }

            return $data;
        }

        private function getAttribute($value)
        {
            if (strpos($value, '_') !== false) {
                $visible = explode('_', $value);
                $attribute = ucfirst($visible[0]) . ucfirst($visible[1]);
            } else {
                $attribute = ucfirst($value);
            }

            $method = 'get' . $attribute . 'Attribute';

            if (method_exists($this, $method)) {
                $data = $this->$method();

                return $data;
            }
            return null;
        }

		private function builder($data)
		{
			$collection = new Collection;
			$items = [];

			if (! empty($data))
			{
				foreach ($data as $key => $value)
				{
					$value = $this->addAttributes($value);
					$data[$key] = $value;
				}

				foreach ($data as $key => $val) {
					$item = new static;
					$item->attributes = $val;
					$items[] = $item;
				}	
			}
			
			$collection->items = $items;

			return $collection;
		}

		public function querySqlStop()
		{
			$this->db::$sqlStop = true;
		}

		public function queryLastSql()
		{
			return $this->db::$lastSql;
		}

		public function toArray()
		{
			return objToArray($this->attributes);
		}

		public function __set($key, $val)
		{
			$this->$key = $val;
		}

		public function __get($key)
		{
			if (isset($this->attributes->$key)) {
				return $this->attributes->$key;
			}
			return null;
		}
	}