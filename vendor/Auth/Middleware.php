<?php

	namespace Vendor\Auth;
	use Request;
	use Exception;

	class Middleware
	{
		private static $config;
		private static $request;
		private static $disallow;

		public function __construct()
		{
		
		}

		public function handle($data)
		{
			self::$config = require('./config/App.php');

			self::$request = new Request;

			$routeMiddleware = self::$config['routeMiddleware'];
			
			self::$disallow = count($data);

			foreach ($data as $key => $value)
			{
				$Middleware = new $routeMiddleware[$value];

				$result = $Middleware->handle(self::$request, function($request) {

					if (get_class($request) == get_class(self::$request))
					{
						self::$disallow -= 1;
					}
				});	 
			}

			if (self::$disallow > 0)
			{
				print_r($result);exit;
			}
		}
	}