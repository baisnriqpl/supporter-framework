<?php
	
	namespace Vendor\Facades;

	class Facade 
	{
		public function __call($method, $parameters)
        {
 		   $instance = $this->provider();
 		   
           return call_user_func_array([$instance, $method], $parameters);
        } 

		public static function __callStatic($method, $parameters)
        {
           $class = new static;
           
		   $instance = $class->provider();

           return call_user_func_array([$instance, $method], $parameters);
        }
	}