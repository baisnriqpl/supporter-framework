<?php

	namespace Vendor\Facades;

	use Vendor\Facades\Facade;
	use Vendor\Auth\Authenticate;
	use Vendor\Facades\FacadesLimiter;

	class Auth extends Facade implements FacadesLimiter
	{
		public function provider()
		{
			return new Authenticate;
		}
	}