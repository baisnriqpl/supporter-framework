<?php

	namespace Vendor\Http;

	class RequestService
	{
		protected $server;
		protected $input;

		public function __construct()
		{
			$this->server = $_SERVER;
			$this->input = file_get_contents('php://input');
		}

		public function server($key = null)
		{
			$server = $this->server;

			if (isset($key))
			{
				if (array_key_exists($key, $server))
				{
					return $server[$key];
				}
				else
				{
					return null;
				}
			}

			return $server;
		}

		public function redirectUrl()
		{
			return $this->server['REDIRECT_URL'];
		}

		public function method($type = null)
		{
			$method = $this->server['REQUEST_METHOD'];

			if ($type === null) 
			{
				return $method;
			}

			if (strtoupper($type) == $method) {
				return true;
			}
			return false;
		}

		public function scriptName()
		{
			return $this->server['SCRIPT_NAME'];
		}

		public function url()
		{
			if (array_key_exists('REDIRECT_URL', $this->server)) {
				$url = str_replace($this->catalogueName(), '', $this->server['REDIRECT_URL']);
				$url = strpos($url, '/') === 0 ? substr($url, 1) : $url;
				$url = strpos($url, '/') === 0 ? substr($url, 1) : $url;

				return $url;
			} 
			return '/';
		}

		public function requestUrl()
		{
			return $this->server['REQUEST_URI'];
		}

		public function currentUrl()
		{
			return $this->catalogueUrl() . '/' . $this->url();
		}

		public function catalogueUrl()
		{
			return $this->protocol() . '://' . $this->host() . '/' .  $this->catalogueName();
		}

		public function fullUrl()
		{
			return $this->protocol() . '://' . $this->host() . $this->requestUrl();
		}

		public function host()
		{
			return $this->server['HTTP_HOST'];
		}

		public function protocol()
		{
			if ((array_key_exists('HTTPS', $this->server) && $this->server['HTTPS'] == 'on') || array_key_exists('REDIRECT_HTTPS', $this->server) && $this->server['REDIRECT_HTTPS'] == 'on')
			{
				return 'https';
			}

			return 'http';
		}

		public function route()
		{
			return $this->url();
		}

		public function catalogueName()
		{
			$name = $this->server['PHP_SELF'];

			return str_replace(['index.php', '/'], '',$name);
		}

		public function scriptFileName()
		{
			return str_replace('/index.php', '', $this->server['SCRIPT_FILENAME']);
		}

		public function all($key = null, $default = null)
		{
			return $this->handleData($this->allData(), $key, $default);
		}

		private function allData()
		{
			$data = $_POST;

			if ($_GET)
			{
				$data = array_merge($data, $_GET);
			}

			if ($json = file_get_contents('php://input'))
			{
				if ($input = json_decode($json, true))
				{
					$data = array_merge($data, $input);
				}
			}

			return $data;
		}

		public function post($key = null, $default = null)
		{
			return $this->handleData($_POST, $key, $default);
		}

		public function get($key = null, $default = null)
		{
			return $this->handleData($_GET, $key, $default);
		}

		public function has($key)
		{
			$data = $this->allData();

			if (array_key_exists($key, $data))
			{
				return true;
			}
			
			return false;
		}

		private function handleData($data, $key = null, $default = null)
		{
			if (! empty($key))
			{
				if (isset($data[$key]))
				{
					return $data[$key];
				}

				if ($default !== null)
				{
					return $default;
				}

				return null;
			}
			return $data;
		}

		public function getIp()
		{
			return $this->server['REMOTE_ADDR'];
		}
	}

