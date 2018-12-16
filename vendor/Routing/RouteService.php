<?php
	
	namespace Vendor\Routing;

	class RouteService
	{
		private static $item = [];
		private $middleware = [];
		private static $groupStack = [];

		public function __construct()
		{
				
		}

		protected function defined($method, $route, $controller, $attributes)
		{
			$attributes = $this->getAttributes($attributes);

			self::$item[] = [
				'route'			=>  $route,
				'controller'	=>	$controller,
				'method'		=>	$method,
				'middleware'	=>	$attributes['middleware'],
				'namespace'		=>	ucfirst($attributes['namespace'])
			];
		}

		protected function getAttributes($attributes)
		{
			if (! array_key_exists('middleware', $attributes))
			{
				$attributes['middleware'] = '';
			}
			elseif(! empty($attributes['middleware']) && is_string($attributes['middleware']))
			{
				$attributes['middleware'] = explode(',', $attributes['middleware']);
			}

			if (! array_key_exists('namespace', $attributes))
			{
				$attributes['namespace'] = '';
			}

			return $attributes;
		}

		public function get($route, $controller, $attributes = [])
		{
			$this->defined('get', $route, $controller, $attributes);
		}

		public function post($route, $controller, $attributes = [])
		{
			$this->defined('post', $route, $controller, $attributes);
		}

		public function any($route, $controller, $attributes = [])
		{
			$this->defined('get, post', $route, $controller, $attributes);
		}

		public function item()
		{
			return self::$item;
		}

		public function group(array $attributes, $callback)
		{
			// self::$groupStack[] = $attributes;
		}

		public function routeParameter($parameter = null)
		{
			$item = self::$item;

			foreach ($item as $key => $value)
			{
				if ($value['route'] == request()->url())
				{
					$route = $item[$key];

					if ($parameter !== null && array_key_exists($parameter, $route))
					{
						return $route[$parameter];
					}

					return $route;
				}	
			}

			return false;
		}
	}