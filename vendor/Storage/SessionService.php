<?php

	namespace Vendor\Storage;
	use Vendor\Storage\RateLimiter;
	use Vendor\Storage\SessionProvider;

	class SessionService implements RateLimiter
	{

		private static $session = [];
		private static $sessionId;

		public function __construct()
		{
			if (! session_id())
			{
				session_start();
			}
		}

		public function all()
		{
			return $_SESSION;
		}

		public function put($key, $value)
		{
			$_SESSION[$key] = $value;
		}

		public function delete($key)
		{
			unset($_SESSION[$key]);
		}

		public function get($key, $value = null)
		{
			if (array_key_exists($key, $_SESSION))
			{
				return $_SESSION[$key];
			}
			elseif(isset($value))
			{
				return $value;
			}

			return false;
		}

		public function remember($key, $data)
		{
			$_SESSION[$key] = $data;

			return $_SESSION[$key];
		}

		public function has($key)
		{
			if (array_key_exists($key, $_SESSION))
			{
				return 1;
			}

			return 0;
		}
	}