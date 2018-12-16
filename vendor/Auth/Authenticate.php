<?php

	namespace Vendor\Auth;
	use Session;
	use App\Models\Users;

	class Authenticate
	{
		public function __construct()
		{

		}

		public function check()
		{
			return Session::has('userInfo');	
		}

		public function login($username, $password)
		{
			$user = Users::where('username', $username)->first()->toArray();

			if (password_verify($password, $user['password']))
			{
				Session::put('userInfo', $user);

				return 1;
			}

			return 0;
		}

		public function logout()
		{
			Session::delete('userInfo');

			if (! Session::has('userInfo'))
			{
				return 1;
			}

			return 0;
		}

		public function user($key = null)
		{
			if ($user = Session::get('userInfo')) 
			{
				if ($key !== null && array_key_exists($key, $user))
				{
					return $user[$key];
				}

				return $user;
			}

			return 0;
		}
	}