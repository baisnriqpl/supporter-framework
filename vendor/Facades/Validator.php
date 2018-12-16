<?php

	namespace Vendor\Facades;
	
	use Vendor\Facades\Facade;
	use Vendor\Verification\Validator as Validate;
	use Vendor\Facades\FacadesLimiter;

	class Validator extends Facade implements FacadesLimiter
	{
		public function provider()
		{
			return new Validate();
		}
	}
