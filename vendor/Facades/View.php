<?php

	namespace Vendor\Facades;

	use Vendor\Facades\Facade;
	use Vendor\View\ViewServerProvider;
	use Vendor\Facades\FacadesLimiter;

	class View extends Facade implements FacadesLimiter
	{
		public function provider()
		{
			return new ViewServerProvider();
		}
	}