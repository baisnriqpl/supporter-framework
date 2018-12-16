<?php

	namespace Vendor\Http;

	class ResponseService
	{
		public function __construct()
		{

		}

		public function json($data)
		{
			print_r(json_encode($data));exit;
		}
	}