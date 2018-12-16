<?php

	namespace App\Middleware;
	use Auth;

	class Authenticate
	{
		public function handle($request, $next)
		{
			if (! Auth::check())
			{
				return redirect('login');	
			}

			return $next($request);
		}
	}