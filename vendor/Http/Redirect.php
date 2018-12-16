<?php

	namespace Vendor\Http;

	class Redirect
	{
		public function back()
		{
			$recode = session('system');
			$url = $recode['lastUrl'];

			$this->goto($url);
		}

		public function goto($url)
		{
			header("Location:{$url}");
		}

		public function action($url)
		{
			$this->goto(request()->catalogueUrl() . '/' . $url);
		}
	}