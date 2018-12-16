<?php		

	require_once './vendor/Helpers/functions.php';
	
	class App
	{
		private $controller;
		private $method;
		private $path = './app/Controllers/';
		private $config;
		private $routeLine = '\\';
		private $route;

		public function __construct()
		{	
			spl_autoload_register('autoLoadClass');	
			$this->config = require_once('./config/App.php');
			$this->loadAliases();
			require_once('./routing/routes.php');
			$this->route = new Route;
		}

		public function load()
		{
			$url = request()->url();
			$method = request()->method();

			$route = $this->route->item();

			if ($route) {

				foreach ($route as $key => $val)
				{
					if ($val['route'] == $url  && strstr(strtoupper($val['method']), $method))
					{
						$routeLine = strpos($val['route'], $this->routeLine) ? $this->routeLine . $val['controller'] : $val['controller'];

						$map = explode('@', $routeLine);
						$this->controller = $map[0];
						$this->method = $map[1];
					}
				}
			}
		}

		public function read()
		{
			if ($controller = $this->controller)
			{
				$namespace = $this->loadNamespace();

				$controller = $namespace ? $namespace . '\\' . $controller : $controller;

				$controller = '\App\Controllers\\' . $controller;

				$this->loadOther();

				$this->loadMiddleware($controller, function($controller) {

					$method = $this->method;
				
					print_r((new $controller)->$method());exit;
				});	
			} 


			if (request()->method('get'))
			{
				return view('common.404');	
			}

			return exit('no route');
		}

		private function loadNamespace()
		{
			return $this->route->routeParameter('namespace');
		}

		private function loadMiddleware($controller, $load)
		{
			$parameter = $this->route->routeParameter('middleware');

			if ($parameter)
			{
				$middleware = new \Vendor\Auth\Middleware();

				$middleware->handle($parameter);	
			}

			$load($controller);		
		}

		private function loadAliases()
		{
			$aliases = $this->config['aliases'];

			foreach ($aliases as $key => $val)
			{
				class_alias($val, $key);	
			}
		}

		public function start()
		{
			return $this->read();
		}

		private function loadOther()
		{
			new Vendor\System\System;
		}
	}