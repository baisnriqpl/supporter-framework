<?php
	
	namespace Vendor\Storage;
	use Vendor\Storage\RateLimiter;
	use Redis;

	class RedisService implements RateLimiter
	{
		private $reids;

		public function __construct()
		{
			$this->redis = new Redis();

			$this->redis->connect('127.0.0.1', 6379);
		}

		public function all()
		{
			echo 'reach RedisService';
		}

		public function set($key, $value)
		{
			$this->redis->set($key, ifArrayToJson($value));
		}

		public function setex($key, $value, $time)
		{
			$this->redis->setex($key, $time, ifArrayToJson($value));
		}

		public function put($key, $value, $time = null)
		{
			if ($time !== null)
			{
				$this->setex($key, $value, $time);
			}
			else
			{
				$this->set($key, $value);
			}
		}

		public function get($key)
		{
			return jsonTransform($this->redis->get($key));
		}

		public function has($key)
		{
			if ($this->redis->exists($key))
			{
				return 1;
			}

			return 0;
		}

		public function delete($key)
		{
			$this->redis->del($key);

			return !$this->has($key);
		}
	}

