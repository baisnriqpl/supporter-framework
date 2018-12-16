<?php

	namespace Vendor\Http;
	use Session;
	use Request;

	class History
	{
		private $keyName = 'system';
		private $system = [];
		private $history = [
			'once'	=>	[]
		];

		public function __construct()
		{
			$system = Session::get($this->keyName);
			$this->system = $system;
			if (isset($system['history']))
			{
				$this->history = $system['history'];
			}
		}

		public function putOnce()
		{
			$data = Request::all(); 

			$this->putOnceData($data);	
		}

		public function getOnce($key  = null)
		{			
			return $this->getAll($key) ?: '';	
		}

		public function getAll($key = null)
		{
			if (isset($this->history['once'][$key]))
			{
				return $this->history['once'][$key];
			}
			elseif(! isset($key))
			{
				return $this->history['once'];
			}

			return '';
		}

		public function error($value, $success = false)
		{
			if (Request::method('post'))
			{
				if ($success == false)
				{
					$color = 'red';
				}
				else 
				{
					$color = 'green';
				}

				$validation = '<span id="systemTip" style="position: fixed;left:0;right:0;top:15%;margin:auto;width:300px;height:30px;background:'.$color.';color:white;text-align:center;line-height:30px;z-index:9999;font-size:12px;"><script type="text/javascript">  setTimeout(function(){$("#systemTip").slideUp();}, 5000);</script>'.$value.'</span>';

				$data = $this->getAll();

				$data['validation'] = $validation;

				$this->putOnceData($data);
			}	
		}

		public function getError()
		{
			$history  = $this->history;

			if (isset($history['once']['validation']))
			{
				return $history['once']['validation'];
			}

			return '';
		}

		public function distroy()
		{
			if (Request::method('get'))
			{
				$history = $this->history;

				if (isset($history['times']))
				{
					$times = $history['times'] + 1;
					$data = $history['once'];

					if ($times > 2)
					{
						
						unset($this->system['history']);

					}
					else 
					{

						$this->system['history'] = [
								'times'		=>	 $times,
								'once'		=>	 $data
								];
					}

					Session::put($this->keyName, $this->system);	
				}
			}
		}

		private function putOnceData($data)
		{
			if (Request::method('post'))
			{
					$this->history	=	[
						'times'		=>	1,
						'once'		=>	$data
					];


				$this->system['history'] = $this->history;

				Session::put($this->keyName, $this->system);	
			}	
		}
	}