<?php

	namespace Vendor\Facades;
	
	use Vendor\Facades\Facade;
	use Vendor\Storage\RedisService;
	use Vendor\Facades\FacadesLimiter;

	class Cache extends Facade implements FacadesLimiter
	{
		public function provider()
		{
			return new RedisService;
		}
	}
