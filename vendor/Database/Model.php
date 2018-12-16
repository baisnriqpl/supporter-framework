<?php

	namespace Vendor\Database;
	use Vendor\Database\Builder;
	use Exception;

	abstract class Model extends Builder
	{
		protected $table;

		protected $primaryKey = 'id';

		public $appends = [];

		public function __construct()
		{
			parent::__construct($this->table);
		}

		public function newQuery()
		{
			return new Builder($this->table);
		}

		public function __set($key, $val)
		{
			@$this->attributes->$key = $val;
		}

		public function __get($key)
		{
			$data = $this->attributes;

			if (isset($data->$key)) {
				return $data->$key;
			}
			return null;
		}

		public function __call($method, $parameters)
        {
        	$method = 'query' . ucfirst($method);
        	if (method_exists($this, $method))
        	{
				return $this->$method(...$parameters);
        	}
        	else
        	{
        		exit(get_called_class() . "::{$method} has no this method!");
        	}
            
        } 

		public static function __callStatic($method, $parameters)
    	{
    		$instance = new static;
    		$method = 'query' . ucfirst($method);

    		if (method_exists($instance,$method))
    		{
    			return $instance->$method(...$parameters);
    		}
    		else
    		{
    			exit(get_called_class() . "::{$method} has no this method!");
    		}
        	
   		}
	}

