<?php

	namespace Vendor\Facades;
	use Vendor\Facades\Facade;				
	use Vendor\Storage\SessionService;
	use Vendor\Facades\FacadesLimiter;

	class Session extends Facade implements FacadesLimiter
	{
		public function provider()
		{
			return new SessionService;
		}
	}