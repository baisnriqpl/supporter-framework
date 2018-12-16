<?php

	namespace Vendor\Facades;
	use Vendor\Facades\Facade;
	use Vendor\Http\RequestService;
	use Vendor\Facades\FacadesLimiter;

	class Request extends Facade implements FacadesLimiter
	{
		public function provider()
		{
			return new RequestService;
		}
		
	}