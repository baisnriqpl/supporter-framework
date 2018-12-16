<?php


	return [
		'aliases' =>	[
			'DB'		=>	 Vendor\Facades\DB::class,
			'Cache' 	=>	 Vendor\Facades\Cache::class,
			'Session'	=>	 Vendor\Facades\Session::class,
			'Request'	=>	 Vendor\Facades\Request::class,
			'Auth'		=>	 Vendor\Facades\Auth::class,
			'Route'		=>	 Vendor\Facades\Route::class,
			'Validator'	=>	 Vendor\Facades\Validator::class,
			'View'		=>	 Vendor\Facades\View::class
		],

		'routeMiddleware' =>	[
			'auth'		=>	 App\Middleware\Authenticate::class
		]

	];