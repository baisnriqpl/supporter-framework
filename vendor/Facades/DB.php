<?php

	namespace Vendor\Facades;

	use Vendor\Database\DB as Mysql;
	use Vendor\Facades\Facade;				
	use Vendor\Facades\FacadesLimiter;

	class DB extends Facade implements FacadesLimiter
	{
		public function provider()
		{
			return new Mysql();
		}
	}