<?php

	namespace Vendor\Verification;
	use Vendor\Verification\Verification;

	class Validator
	{
		public function make(array $rules, array $data, array $lan = [])
		{	
			return new Verification($rules, $data, $lan);
		}
	}











	