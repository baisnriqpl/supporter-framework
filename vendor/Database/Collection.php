<?php

	namespace Vendor\Database;
	use Vendor\Database\Builder;

	class Collection
	{
		public $items = [];

		public function toArray()
		{
			$data = [];
			if ($items = $this->items) {
				foreach ($items as $key => $value) {
					$data[$key] = objToArray($value->attributes);
				}
			}
			return $data;
		}

		public function count()
		{
			return count($this->items);
		}

		public function toJson()
		{
			return json_encode($this->toArray());
		}

		public function __set($key, $val)
		{
			$this->$key = $val;
		}
	}