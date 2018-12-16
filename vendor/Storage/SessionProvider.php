<?php

	namespace Vendor\Storage;

	class SessionProvider
	{
		protected static $session;

		public function __construct()
		{
			if (! session_id())
			{
				// session_start();
			}
			
			SessionProvider::$session = $_SESSION;
		}

		public function all()
		{
			return SessionProvider::$session;
		}
	}