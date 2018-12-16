<?php
	
	namespace Vendor\Facades;

	use Vendor\Facades\Facade;
	use Vendor\Routing\RouteService;
	use Vendor\Facades\FacadesLimiter;

	class Route extends Facade implements FacadesLimiter
	{
		public function provider()
		{
			return new RouteService();
		}
	}