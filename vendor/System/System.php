<?php

	namespace Vendor\System;

	class System
	{
		public $recode;
		private $keyName = 'system';

		public function __construct()
		{
			date_default_timezone_set('PRC');

			$history = new \Vendor\Http\History;
			$history->putOnce();
			$history->distroy();
			$this->recodeUrl();
		}

		public function recodeUrl()
		{
			$request = request();

			$recode = session()->get($this->keyName, []);
			
			$recode['lastUrl'] = isset($recode['currentUrl']) ? $recode['currentUrl'] : '';
			$recode['currentUrl'] = $request->fullUrl();

			session($this->keyName, $recode);	
		}
	}