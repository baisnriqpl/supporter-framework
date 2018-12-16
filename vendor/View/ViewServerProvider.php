<?php

	namespace Vendor\View;

	class ViewServerProvider
	{
		protected $path = 'resources/views/';
		protected $bladeSign = '.blade.php';
		protected $dataSignLeft = '{{';
		protected $dataSignRight = '}}';
		protected $codeSign = '@';
		protected $filePath = 'storage/views/';
		protected $phpSign = ['foreach', 'for', 'if'];
		protected $phpEnd = ['endforeach', 'endfor', 'else', 'endif']; 

		public function __construct()
		{
			$this->handleSign();
		}

		private function handleSign()
		{
			$config = require('./config/App.php');

			if (array_key_exists('dataSign', $config))
			{
				$sign = $config['dataSign'];
				$left = $sign['left'];
				$right = $sign['right'];
			}
			else 
			{
				$left = $this->dataSignLeft;
				$right = $this->dataSignRight;
			}
			
			$signLeft = substr($left, 0, 1);
			$signRight = substr($right, 0, 1);
			$leftCount = strlen($left);
			$rightCount = strlen($right);;

			$sleft = '';
			for($k = 0; $k < $leftCount; $k ++)
			{
				$sleft .= '\\'.$signLeft;
			}

			$sright = '';
			for($k = 0; $k < $rightCount; $k ++)
			{
				$sright .= '\\'.$signRight;
			}

			$this->dataSignLeft = $sleft;
			$this->dataSignRight = $sright;
		}

		private function getFile($path)
		{
			$file = str_replace('.', '/', $path). $this->bladeSign;

			$path = $this->path . $file;

			return file_get_contents($path);
		}

		public function make($path, array $_systemData = [])
		{
			
			$file = $this->getFile($path);

			$blade = $this->handleFunc($file, $_systemData);

			$newFile = $this->filePath . md5($file) . '.php';

			$this->create($newFile, $blade);

			$this->load($newFile, $_systemData);
		}

		private function load($newFile, $_systemData)
		{
			extract($_systemData);

			require($newFile);	
		}

		private function handleSysFunc($preg, $blade, $sign, $marke = ';', $echo  = '')
		{
			$echo = $echo ? 'echo' : '';

			preg_match_all($preg, $blade, $sysFunc);

			$sysFunc = $sysFunc[0];

			if ($sysFunc)
			{
				foreach ($sysFunc as $key => $value)
				{
					$val = preg_replace($sign, '', $value);
					$blade = str_replace($value, '<?php ' . $echo . $val . $marke . ' ?>', $blade);	
				}
			}

			return $blade;
		}

		private function handleFunc($blade, $_systemData)
		{	

			$filder =  "/\\" . $this->codeSign . "/";

			//处理else
			$preg = "/\\" . $this->codeSign . "else[a-z]{0,2}\s/";

			$blade = $this->handleSysFunc($preg, $blade, $filder, ':');

			//转换一带@的函数结尾(endif, endfor, endforeach)
			$preg = "/\\" . $this->codeSign . "end[a-z]+[f|r|h]/";

			$blade = $this->handleSysFunc($preg, $blade, $filder);

			//转换带@的函数(if, for, foreach)
			$preg = "/\\" . $this->codeSign . "[a-z]+[f|r|h][\s]{0,1}\((.*)\)/";

			$blade = $this->handleSysFunc($preg, $blade,  $filder, ':');

			$filder = '/'.$this->dataSignLeft . '|' . $this->dataSignRight.'/';

			//转换变量输出
			$preg = "/{$this->dataSignLeft}[\s|\S]{0,5}\\$(.*){$this->dataSignRight}/U";

			$blade = $this->handleSysFunc($preg, $blade, $filder, ';', 1);

			//转换输出函数
			$preg = "/{$this->dataSignLeft}[\s|\S]{0,5}[a-zA-Z]+\((.*)\)[\s|\S]+{$this->dataSignRight}/U";

			$blade = $this->handleSysFunc($preg, $blade, $filder, ';', 1);
			
			return $blade;
		}

		private function create($fileName, $content)
		{
			$dir = './storage/views';
			if (! is_dir($dir))
			{
				mkdir($dir, 777, true); 
			}
			file_put_contents($fileName, $content);
		}
	}